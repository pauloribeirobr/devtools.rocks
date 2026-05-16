<?php

declare(strict_types=1);

namespace App;

class SeoHelper
{
    public function __construct(private readonly string $lang, private readonly array $allLanguages)
    {
    }

    public function localizedHomeHreflangTags(): string
    {
        // Homes existem em todos os idiomas e servem como fallback x-default.
        $urls = [];

        foreach (array_keys($this->allLanguages) as $lang) {
            $urls[$lang] = SITE_URL . "/{$lang}/";
        }

        return $this->hreflangFromUrls($urls, $urls[SITE_DEFAULT_LANG] ?? reset($urls));
    }

    public function localizedStaticHreflangTags(string $pageKey): string
    {
        // Slugs estaticos, como about/contact/privacy, sao traduzidos por idioma.
        $urls = [];

        foreach ($this->allLanguages as $lang => $language) {
            $slug = $language['url_slugs'][$pageKey] ?? $pageKey;
            $urls[$lang] = SITE_URL . "/{$lang}/{$slug}/";
        }

        return $this->hreflangFromUrls($urls, $urls[SITE_DEFAULT_LANG] ?? reset($urls));
    }

    public function localizedTaxonomyHreflangTags(string $routeKey, string $slug): string
    {
        // Taxonomias reaproveitam o mesmo slug de termo e traduzem apenas o segmento da rota.
        $urls = [];

        foreach ($this->allLanguages as $lang => $language) {
            $route = $language['url_slugs'][$routeKey] ?? $routeKey;
            $urls[$lang] = SITE_URL . "/{$lang}/{$route}/{$slug}/";
        }

        return $this->hreflangFromUrls($urls, $urls[$this->lang] ?? reset($urls));
    }

    public function postHreflangTags(array $post): string
    {
        $urls = [];
        $translations = $post['translations'] ?? [];

        // Inclui a propria URL atual junto das traducoes declaradas no frontmatter.
        $translations[$this->lang] = $post['slug'] ?? '';

        foreach ($translations as $lang => $slug) {
            if (!isset($this->allLanguages[$lang]) || $slug === '') {
                continue;
            }

            $urls[$lang] = SITE_URL . "/{$lang}/{$slug}/";
        }

        return $this->hreflangFromUrls($urls, $urls[SITE_DEFAULT_LANG] ?? ($urls[$this->lang] ?? reset($urls)));
    }

    public function webSiteSchema(): string
    {
        // Descreve o site como entidade raiz para mecanismos de busca.
        return $this->script([
            '@context' => 'https://schema.org',
            '@type' => 'WebSite',
            'name' => SITE_NAME,
            'url' => SITE_URL,
            'inLanguage' => $this->locale(),
            'description' => SITE_TAGLINE,
        ]);
    }

    public function webPageSchema(string $canonical, string $title, string $description, string $type = 'WebPage'): string
    {
        // Schema generico usado por home, paginas estaticas e taxonomias.
        return $this->script([
            '@context' => 'https://schema.org',
            '@type' => $type,
            'name' => $title,
            'description' => $description,
            'url' => $canonical,
            'inLanguage' => $this->locale(),
            'isPartOf' => [
                '@type' => 'WebSite',
                'name' => SITE_NAME,
                'url' => SITE_URL,
            ],
        ]);
    }

    public function collectionPageSchema(string $canonical, string $title, string $description, array $posts = []): string
    {
        // CollectionPage ajuda categoria/tag/hub a declarar a lista de URLs internas.
        $items = [];

        foreach (array_values($posts) as $index => $post) {
            $items[] = [
                '@type' => 'ListItem',
                'position' => $index + 1,
                'url' => SITE_URL . ($post['url'] ?? ''),
                'name' => $post['title'] ?? $post['slug'] ?? '',
            ];
        }

        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'CollectionPage',
            'name' => $title,
            'description' => $description,
            'url' => $canonical,
            'inLanguage' => $this->locale(),
        ];

        if ($items !== []) {
            $schema['mainEntity'] = [
                '@type' => 'ItemList',
                'itemListElement' => $items,
            ];
        }

        return $this->script($schema);
    }

    public function schemaByType(array $post, string $canonical): string
    {
        $type = $post['meta']['type'] ?? 'article';

        return match ($type) {
            'tutorial' => $this->howToSchema($post, $canonical),
            'review' => $this->reviewSchema($post, $canonical),
            'comparison', 'list' => $this->articleWithItemListSchema($post, $canonical),
            'hub' => $this->hubSchema($post, $canonical),
            default => $this->articleSchema($post, $canonical),
        };
    }

    public function articleSchema(array $post, string $canonical): string
    {
        $meta = $post['meta'];
        $schema = $this->baseArticleSchema($meta, $canonical, 'TechArticle');
        // Artigo isolado precisa do @context porque nao esta dentro de um @graph.
        $schema = ['@context' => 'https://schema.org'] + $schema;

        return $this->script($schema);
    }

    public function howToSchema(array $post, string $canonical): string
    {
        $meta = $post['meta'];
        $steps = [];

        // Se o frontmatter tiver steps, expomos como HowToStep.
        foreach (($meta['steps'] ?? []) as $index => $step) {
            $steps[] = [
                '@type' => 'HowToStep',
                'position' => $index + 1,
                'name' => $step['name'] ?? $step['title'] ?? ('Step ' . ($index + 1)),
                'text' => $step['text'] ?? $step['description'] ?? '',
            ];
        }

        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'HowTo',
            'name' => $meta['title'] ?? '',
            'description' => $meta['description'] ?? '',
            'url' => $canonical,
            'inLanguage' => $this->locale(),
            'totalTime' => 'PT' . (int) ($meta['reading_time'] ?? 10) . 'M',
            'author' => $this->author(),
        ];

        if ($steps !== []) {
            $schema['step'] = $steps;
        }

        return $this->script($schema);
    }

    public function reviewSchema(array $post, string $canonical): string
    {
        $meta = $post['meta'];
        $review = $meta['review'] ?? [];
        $affiliate = $meta['affiliate'][0] ?? [];
        $itemName = $review['item_name'] ?? $review['name'] ?? $affiliate['name'] ?? $meta['title'] ?? '';

        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'Review',
            'name' => $meta['title'] ?? '',
            'description' => $meta['description'] ?? '',
            'url' => $canonical,
            'inLanguage' => $this->locale(),
            'author' => $this->author(),
            'datePublished' => $meta['date'] ?? '',
            'dateModified' => $meta['updated'] ?? $meta['date'] ?? '',
            'itemReviewed' => [
                '@type' => $review['item_type'] ?? 'SoftwareApplication',
                'name' => $itemName,
            ],
            'reviewRating' => [
                '@type' => 'Rating',
                'ratingValue' => (float) ($review['rating'] ?? $affiliate['rating'] ?? 4),
                'bestRating' => (float) ($review['best_rating'] ?? 5),
                'worstRating' => (float) ($review['worst_rating'] ?? 1),
            ],
        ];

        return $this->script($schema);
    }

    public function articleWithItemListSchema(array $post, string $canonical): string
    {
        $meta = $post['meta'];
        $graph = [$this->baseArticleSchema($meta, $canonical, 'TechArticle')];
        $items = $this->itemListFromMeta($meta);

        if ($items !== []) {
            $graph[] = [
                '@type' => 'ItemList',
                '@id' => $canonical . '#item-list',
                'name' => $meta['title'] ?? '',
                'itemListElement' => $items,
            ];
        }

        return $this->script([
            '@context' => 'https://schema.org',
            '@graph' => $graph,
        ]);
    }

    public function hubSchema(array $post, string $canonical): string
    {
        $meta = $post['meta'];

        return $this->script([
            '@context' => 'https://schema.org',
            '@type' => 'CollectionPage',
            'name' => $meta['title'] ?? '',
            'description' => $meta['description'] ?? '',
            'url' => $canonical,
            'inLanguage' => $this->locale(),
            'author' => $this->author(),
        ]);
    }

    public function faqSchema(array $post): string
    {
        $faq = $post['meta']['faq'] ?? [];

        if ($faq === []) {
            return '';
        }

        $items = [];

        foreach ($faq as $item) {
            $question = $item['question'] ?? $item['pergunta'] ?? '';
            $answer = $item['answer'] ?? $item['resposta'] ?? '';

            if ($question === '' || $answer === '') {
                continue;
            }

            $items[] = [
                '@type' => 'Question',
                'name' => $question,
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text' => $answer,
                ],
            ];
        }

        if ($items === []) {
            return '';
        }

        return $this->script([
            '@context' => 'https://schema.org',
            '@type' => 'FAQPage',
            'mainEntity' => $items,
        ]);
    }

    public function breadcrumbSchema(array $items): string
    {
        $list = [];

        // Converte breadcrumbs visuais para o formato esperado pelo Schema.org.
        foreach ($items as $index => $item) {
            $entry = [
                '@type' => 'ListItem',
                'position' => $index + 1,
                'name' => $item['label'],
            ];

            if (!empty($item['url'])) {
                $entry['item'] = $item['url'];
            }

            $list[] = $entry;
        }

        return $this->script([
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => $list,
        ]);
    }

    private function baseArticleSchema(array $meta, string $canonical, string $type): array
    {
        $schema = [
            '@type' => $type,
            '@id' => $canonical . '#article',
            'headline' => $meta['title'] ?? '',
            'description' => $meta['description'] ?? '',
            'datePublished' => $meta['date'] ?? '',
            'dateModified' => $meta['updated'] ?? $meta['date'] ?? '',
            'inLanguage' => $this->locale(),
            'mainEntityOfPage' => [
                '@type' => 'WebPage',
                '@id' => $canonical,
            ],
            'author' => $this->author(),
            'publisher' => $this->publisher(),
        ];

        if (!empty($meta['image'])) {
            $schema['image'] = $this->postImageUrl($meta);
        }

        if (!empty($meta['category_name'])) {
            $schema['articleSection'] = $meta['category_name'];
        }

        if (!empty($meta['tags'])) {
            $schema['keywords'] = implode(', ', $meta['tags']);
        }

        return $schema;
    }

    private function itemListFromMeta(array $meta): array
    {
        $source = $meta['items'] ?? $meta['affiliate'] ?? [];
        $items = [];

        // Comparativos e listas usam items/affiliate para montar ItemList.
        foreach (array_values($source) as $index => $item) {
            $name = is_array($item) ? ($item['name'] ?? '') : (string) $item;

            if ($name === '') {
                continue;
            }

            $items[] = [
                '@type' => 'ListItem',
                'position' => $index + 1,
                'name' => $name,
            ];
        }

        return $items;
    }

    private function hreflangFromUrls(array $urls, string|false $defaultUrl): string
    {
        $tags = [];

        foreach ($urls as $lang => $url) {
            if (!isset($this->allLanguages[$lang])) {
                continue;
            }

            $locale = $this->allLanguages[$lang]['locale'];
            $tags[] = '<link rel="alternate" hreflang="' . e($locale) . '" href="' . e($url) . '">';
        }

        if (is_string($defaultUrl) && $defaultUrl !== '') {
            $tags[] = '<link rel="alternate" hreflang="x-default" href="' . e($defaultUrl) . '">';
        }

        return $tags === [] ? '' : implode("\n", $tags) . "\n";
    }

    private function postImageUrl(array $meta): string
    {
        $image = (string) $meta['image'];

        if (str_starts_with($image, 'http://') || str_starts_with($image, 'https://')) {
            return $image;
        }

        $slug = $meta['slug'] ?? '';

        return SITE_URL . "/content/{$this->lang}/{$slug}/{$image}";
    }

    private function author(): array
    {
        return [
            '@type' => 'Person',
            'name' => AUTHOR_NAME,
            'url' => AUTHOR_URL,
        ];
    }

    private function publisher(): array
    {
        return [
            '@type' => 'Organization',
            'name' => SITE_NAME,
            'url' => SITE_URL,
            'logo' => [
                '@type' => 'ImageObject',
                'url' => OG_IMAGE,
            ],
        ];
    }

    private function locale(): string
    {
        return $this->allLanguages[$this->lang]['locale'] ?? $this->lang;
    }

    private function script(array $schema): string
    {
        // Centraliza a serializacao JSON-LD para evitar duplicacao nos templates.
        return '<script type="application/ld+json">'
            . json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
            . '</script>' . "\n";
    }
}
