<?php echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n"; ?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
        xmlns:xhtml="http://www.w3.org/1999/xhtml">
    <!-- Sitemap unificado: homes por idioma e posts publicados. -->
    <?php foreach ($allPosts as $lang => $posts): ?>
        <url>
            <loc><?= e(SITE_URL . "/{$lang}/") ?></loc>
        </url>
        <?php foreach ($posts as $post): ?>
            <url>
                <loc><?= e(SITE_URL . "/{$lang}/{$post['slug']}/") ?></loc>
                <?php if (!empty($post['updated']) || !empty($post['date'])): ?>
                    <lastmod><?= e($post['updated'] ?? $post['date']) ?></lastmod>
                <?php endif; ?>
                <?php if (!empty($post['translations'])): ?>
                    <?php foreach ($post['translations'] as $altLang => $altSlug): ?>
                        <xhtml:link rel="alternate"
                                    hreflang="<?= e($altLang) ?>"
                                    href="<?= e(SITE_URL . "/{$altLang}/{$altSlug}/") ?>" />
                    <?php endforeach; ?>
                <?php endif; ?>
            </url>
        <?php endforeach; ?>
    <?php endforeach; ?>
</urlset>
