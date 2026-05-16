<nav class="lang-switcher" aria-label="Languages">
    <?php foreach ($i18n->allLanguages() as $code => $language): ?>
        <?php
        // Em posts, tenta apontar para o slug traduzido declarado no frontmatter.
        $href = "/{$code}/";
        if (isset($post['meta']['translations'][$code])) {
            $href = "/{$code}/" . $post['meta']['translations'][$code] . '/';
        } elseif ($code === $i18n->getLocale() && !empty($post['meta']['slug'])) {
            $href = "/{$code}/" . $post['meta']['slug'] . '/';
        }
        ?>
        <a href="<?= e($href) ?>" hreflang="<?= e($language['locale']) ?>" class="<?= $code === $i18n->getLocale() ? 'active' : '' ?>">
            <?= e($language['flag']) ?>
        </a>
    <?php endforeach; ?>
</nav>
