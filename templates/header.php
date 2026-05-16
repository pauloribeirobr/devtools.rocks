<!DOCTYPE html>
<html lang="<?= e($i18n->getLanguage()['locale'] ?? $i18n->getLocale()) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($page_title ?? SITE_NAME) ?></title>
    <meta name="description" content="<?= e($meta_description ?? '') ?>">
    <?php if (!empty($meta_robots)): ?>
        <meta name="robots" content="<?= e($meta_robots) ?>">
    <?php endif; ?>
    <link rel="canonical" href="<?= e($canonical ?? SITE_URL) ?>">
    <?= $hreflang_tags ?? '' ?>
    <meta property="og:title" content="<?= e($page_title ?? SITE_NAME) ?>">
    <meta property="og:description" content="<?= e($meta_description ?? '') ?>">
    <meta property="og:image" content="<?= e($og_image ?? OG_IMAGE) ?>">
    <meta property="og:url" content="<?= e($canonical ?? SITE_URL) ?>">
    <meta property="og:type" content="website">
    <meta property="og:locale" content="<?= e($i18n->getLanguage()['locale'] ?? $i18n->getLocale()) ?>">
    <meta name="twitter:card" content="summary_large_image">
    <?= $schema_breadcrumb ?? '' ?>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
    <!-- Header compartilhado: logo, navegacao principal e troca de idioma. -->
    <header class="site-header">
        <a class="site-logo" href="/<?= e($i18n->getLocale()) ?>/"><?= e(SITE_NAME) ?></a>
        <nav class="site-nav" aria-label="Primary">
            <a href="/<?= e($i18n->getLocale()) ?>/"><?= e($i18n->t('nav.home')) ?></a>
            <a href="/<?= e($i18n->getLocale()) ?>/<?= e($i18n->urlSlug('about')) ?>/"><?= e($i18n->t('nav.about')) ?></a>
            <a href="/<?= e($i18n->getLocale()) ?>/<?= e($i18n->urlSlug('contact')) ?>/"><?= e($i18n->t('nav.contact')) ?></a>
        </nav>
        <?php require TEMPLATES_PATH . '/partials/lang-switcher.php'; ?>
    </header>
    <!-- Conteudo especifico de cada rota entra dentro do main. -->
    <main class="site-main">
