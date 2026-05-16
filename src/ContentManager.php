<?php

declare(strict_types=1);

namespace App;

use DateTimeInterface;
use Parsedown;
use Symfony\Component\Yaml\Yaml;

class ContentManager
{
    private string $contentPath;

    private Parsedown $parsedown;

    public function __construct(private readonly string $lang)
    {
        // Cada idioma tem sua propria arvore de conteudo.
        $this->contentPath = CONTENT_PATH . '/' . $lang;
        $this->parsedown = new Parsedown();
        $this->parsedown->setSafeMode(false);

        if (!is_dir(CACHE_PATH)) {
            mkdir(CACHE_PATH, 0755, true);
        }
    }

    public function getAllPosts(): array
    {
        $cacheFile = CACHE_PATH . "/all_posts_{$this->lang}.php";

        // Lista de posts publicados e cacheada por idioma para evitar reparse.
        if (CACHE_ENABLED && $this->isCacheValid($cacheFile)) {
            return require $cacheFile;
        }

        $posts = [];
        $today = date('Y-m-d');
        $dirs = glob($this->contentPath . '/*', GLOB_ONLYDIR) ?: [];

        foreach ($dirs as $dir) {
            $indexPath = $dir . '/index.md';
            if (!file_exists($indexPath)) {
                continue;
            }

            // Cada post vive em content/{lang}/{slug}/index.md.
            $parsed = $this->parseMarkdown((string) file_get_contents($indexPath));
            $meta = $this->normalizeMeta($parsed['meta']);

            if (($meta['status'] ?? 'draft') !== 'published') {
                continue;
            }

            if (($meta['date'] ?? '9999-12-31') > $today) {
                continue;
            }

            $slug = basename($dir);
            $meta['slug'] = $slug;
            $meta['url'] = "/{$this->lang}/{$slug}/";
            $meta['excerpt'] = $meta['excerpt'] ?? $this->excerpt($parsed['html']);

            $posts[] = $meta;
        }

        usort($posts, static function (array $a, array $b): int {
            return strcmp($b['date'] ?? '2000-01-01', $a['date'] ?? '2000-01-01');
        });

        if (CACHE_ENABLED) {
            file_put_contents($cacheFile, '<?php return ' . var_export($posts, true) . ';' . PHP_EOL);
        }

        return $posts;
    }

    public function getPost(string $slug): ?array
    {
        // Slug restrito evita path traversal e URLs fora do padrao.
        if (!preg_match('/^[a-z0-9-]+$/', $slug)) {
            return null;
        }

        $path = "{$this->contentPath}/{$slug}/index.md";
        if (!file_exists($path)) {
            return null;
        }

        $parsed = $this->parseMarkdown((string) file_get_contents($path));
        $parsed['meta'] = $this->normalizeMeta($parsed['meta']);

        if (($parsed['meta']['status'] ?? 'draft') !== 'published') {
            return null;
        }

        if (($parsed['meta']['date'] ?? '9999-12-31') > date('Y-m-d')) {
            return null;
        }

        $parsed['meta']['slug'] = $slug;
        $parsed['meta']['url'] = "/{$this->lang}/{$slug}/";

        return $parsed;
    }

    public function getPostsByCategory(string $categorySlug): array
    {
        return array_values(array_filter(
            $this->getAllPosts(),
            static fn(array $post): bool => ($post['category'] ?? '') === $categorySlug
        ));
    }

    public function getPostsByTag(string $tagSlug): array
    {
        return array_values(array_filter(
            $this->getAllPosts(),
            static fn(array $post): bool => in_array($tagSlug, $post['tags'] ?? [], true)
        ));
    }

    public function getCategories(): array
    {
        $categories = [];

        foreach ($this->getAllPosts() as $post) {
            $slug = $post['category'] ?? null;
            if (!$slug) {
                continue;
            }

            $categories[$slug] = $post['category_name'] ?? $slug;
        }

        ksort($categories);

        return $categories;
    }

    public function getRelatedPosts(string $slug, string $category, int $limit = 4): array
    {
        if ($category === '') {
            return [];
        }

        $related = array_values(array_filter(
            $this->getAllPosts(),
            static fn(array $post): bool => $post['slug'] !== $slug && ($post['category'] ?? '') === $category
        ));

        return array_slice($related, 0, $limit);
    }

    public function getSpokesForHub(string $hubSlug): array
    {
        return array_values(array_filter(
            $this->getAllPosts(),
            static fn(array $post): bool => ($post['hub'] ?? '') === $hubSlug
        ));
    }

    public function getHubPost(string $hubSlug): ?array
    {
        foreach ($this->getAllPosts() as $post) {
            if ($post['slug'] === $hubSlug && ($post['type'] ?? '') === 'hub') {
                return $post;
            }
        }

        return null;
    }

    private function parseMarkdown(string $content): array
    {
        // Frontmatter YAML e opcional; sem ele, todo arquivo vira corpo Markdown.
        if (!preg_match('/^---\s*\n(.*?)\n---\s*\n(.*)$/s', $content, $matches)) {
            return [
                'meta' => [],
                'html' => $this->parsedown->text($content),
            ];
        }

        return [
            'meta' => Yaml::parse($matches[1]) ?? [],
            'html' => $this->parsedown->text($matches[2]),
        ];
    }

    private function normalizeMeta(array $meta): array
    {
        // Normaliza datas para string porque YAML pode retornar objetos DateTime.
        foreach (['date', 'updated'] as $key) {
            if (($meta[$key] ?? null) instanceof DateTimeInterface) {
                $meta[$key] = $meta[$key]->format('Y-m-d');
            } elseif (isset($meta[$key])) {
                $meta[$key] = (string) $meta[$key];
            }
        }

        return $meta;
    }

    private function isCacheValid(string $file): bool
    {
        return file_exists($file) && (time() - filemtime($file)) < CACHE_TTL;
    }

    private function excerpt(string $html, int $length = 160): string
    {
        // Excerpt padrao para listagens quando o frontmatter nao define um.
        $text = trim(preg_replace('/\s+/', ' ', strip_tags($html)) ?? '');
        $slice = function_exists('mb_substr') ? mb_substr($text, 0, $length) : substr($text, 0, $length);

        return $slice . (strlen($text) > $length ? '...' : '');
    }
}
