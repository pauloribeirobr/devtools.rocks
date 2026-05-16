<?php echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n"; ?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
        xmlns:xhtml="http://www.w3.org/1999/xhtml">
    <!-- Sitemap unificado: homes, paginas estaticas e posts publicados. -->
    <?php foreach ($allPosts as $lang => $posts): ?>
        <url>
            <loc><?= e(SITE_URL . "/{$lang}/") ?></loc>
            <?php foreach (array_keys($languages) as $altLang): ?>
                <xhtml:link rel="alternate"
                            hreflang="<?= e($languages[$altLang]['locale']) ?>"
                            href="<?= e(SITE_URL . "/{$altLang}/") ?>" />
            <?php endforeach; ?>
            <xhtml:link rel="alternate" hreflang="x-default" href="<?= e(SITE_URL . '/' . SITE_DEFAULT_LANG . '/') ?>" />
        </url>
        <?php foreach (['about', 'contact', 'privacy'] as $pageKey): ?>
            <?php $currentSlug = $languages[$lang]['url_slugs'][$pageKey] ?? $pageKey; ?>
            <url>
                <loc><?= e(SITE_URL . "/{$lang}/{$currentSlug}/") ?></loc>
                <?php foreach ($languages as $altLang => $language): ?>
                    <?php $altSlug = $language['url_slugs'][$pageKey] ?? $pageKey; ?>
                    <xhtml:link rel="alternate"
                                hreflang="<?= e($language['locale']) ?>"
                                href="<?= e(SITE_URL . "/{$altLang}/{$altSlug}/") ?>" />
                <?php endforeach; ?>
                <?php $defaultSlug = $languages[SITE_DEFAULT_LANG]['url_slugs'][$pageKey] ?? $pageKey; ?>
                <xhtml:link rel="alternate"
                            hreflang="x-default"
                            href="<?= e(SITE_URL . '/' . SITE_DEFAULT_LANG . "/{$defaultSlug}/") ?>" />
            </url>
        <?php endforeach; ?>
        <?php foreach ($posts as $post): ?>
            <url>
                <loc><?= e(SITE_URL . "/{$lang}/{$post['slug']}/") ?></loc>
                <?php if (!empty($post['updated']) || !empty($post['date'])): ?>
                    <lastmod><?= e($post['updated'] ?? $post['date']) ?></lastmod>
                <?php endif; ?>
                <xhtml:link rel="alternate"
                            hreflang="<?= e($languages[$lang]['locale']) ?>"
                            href="<?= e(SITE_URL . "/{$lang}/{$post['slug']}/") ?>" />
                <?php if (!empty($post['translations'])): ?>
                    <?php foreach ($post['translations'] as $altLang => $altSlug): ?>
                        <?php if (empty($languages[$altLang])) continue; ?>
                        <xhtml:link rel="alternate"
                                    hreflang="<?= e($languages[$altLang]['locale']) ?>"
                                    href="<?= e(SITE_URL . "/{$altLang}/{$altSlug}/") ?>" />
                    <?php endforeach; ?>
                <?php endif; ?>
                <?php if ($lang === SITE_DEFAULT_LANG): ?>
                    <xhtml:link rel="alternate"
                                hreflang="x-default"
                                href="<?= e(SITE_URL . "/{$lang}/{$post['slug']}/") ?>" />
                <?php endif; ?>
            </url>
        <?php endforeach; ?>
    <?php endforeach; ?>
</urlset>
