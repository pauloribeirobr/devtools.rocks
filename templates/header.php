<!DOCTYPE html>
<html lang="<?= e($i18n->getLanguage()['locale'] ?? $i18n->getLocale()) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($page_title ?? SITE_NAME) ?></title>
    <meta name="description" content="<?= e($meta_description ?? '') ?>">
    <meta name="author" content="<?= e(AUTHOR_NAME) ?>">
    <?php if (!empty($meta_robots)): ?>
        <meta name="robots" content="<?= e($meta_robots) ?>">
    <?php endif; ?>
    <link rel="canonical" href="<?= e($canonical ?? SITE_URL) ?>">
    <!-- Hreflang e gerado pela rota atual, respeitando slugs traduzidos. -->
    <?= $hreflang_tags ?? '' ?>
    <meta property="og:site_name" content="<?= e(SITE_NAME) ?>">
    <meta property="og:title" content="<?= e($page_title ?? SITE_NAME) ?>">
    <meta property="og:description" content="<?= e($meta_description ?? '') ?>">
    <meta property="og:image" content="<?= e($og_image ?? OG_IMAGE) ?>">
    <meta property="og:url" content="<?= e($canonical ?? SITE_URL) ?>">
    <meta property="og:type" content="<?= e($og_type ?? 'website') ?>">
    <meta property="og:locale" content="<?= e($i18n->getLanguage()['locale'] ?? $i18n->getLocale()) ?>">
    <?php if (!empty($post['meta']['date'])): ?>
        <meta property="article:published_time" content="<?= e($post['meta']['date']) ?>">
    <?php endif; ?>
    <?php if (!empty($post['meta']['updated'])): ?>
        <meta property="article:modified_time" content="<?= e($post['meta']['updated']) ?>">
    <?php endif; ?>
    <?php if (!empty($post['meta']['tags'])): ?>
        <?php foreach ($post['meta']['tags'] as $tag): ?>
            <meta property="article:tag" content="<?= e($tag) ?>">
        <?php endforeach; ?>
    <?php endif; ?>
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?= e($page_title ?? SITE_NAME) ?>">
    <meta name="twitter:description" content="<?= e($meta_description ?? '') ?>">
    <meta name="twitter:image" content="<?= e($og_image ?? OG_IMAGE) ?>">
    <!-- JSON-LD fica no head para ser encontrado mesmo sem executar JS. -->
    <?= $schema_page ?? '' ?>
    <?= $schema_article ?? '' ?>
    <?= $schema_faq ?? '' ?>
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
        <?php if (!empty($breadcrumbItems)): ?>
            <?php require TEMPLATES_PATH . '/partials/breadcrumbs.php'; ?>
        <?php endif; ?>
