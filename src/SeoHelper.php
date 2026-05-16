<?php

declare(strict_types=1);

namespace App;

class SeoHelper
{
    public function __construct(private readonly string $lang, private readonly array $allLanguages)
    {
    }

    public function hreflangTags(array $post, string $currentPath): string
    {
        $tags = [];
        $translations = $post['translations'] ?? [];
        // Inclui a propria URL atual junto das traducoes declaradas no post.
        $translations[$this->lang] = $post['slug'] ?? trim($currentPath, '/');

        foreach ($translations as $lang => $slug) {
            if (!isset($this->allLanguages[$lang])) {
                continue;
            }

            $locale = $this->allLanguages[$lang]['locale'];
            $url = SITE_URL . "/{$lang}/{$slug}/";
            $tags[] = '<link rel="alternate" hreflang="' . e($locale) . '" href="' . e($url) . '">';
        }

        if (isset($translations[SITE_DEFAULT_LANG])) {
            $url = SITE_URL . '/' . SITE_DEFAULT_LANG . '/' . $translations[SITE_DEFAULT_LANG] . '/';
            $tags[] = '<link rel="alternate" hreflang="x-default" href="' . e($url) . '">';
        }

        return implode("\n", $tags);
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

    private function script(array $schema): string
    {
        // Centraliza a serializacao JSON-LD para evitar duplicacao nos templates.
        return '<script type="application/ld+json">'
            . json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
            . '</script>';
    }
}
