<header class="page-heading">
    <!-- Tags ajudam navegacao interna, mas recebem noindex no controller. -->
    <h1><?= e($i18n->t('tag.title', ['tag' => $tagName])) ?></h1>
    <p><?= e($meta_description) ?></p>
</header>

<section class="post-list" aria-label="Posts">
    <!-- Lista todos os posts publicados com a tag solicitada. -->
    <?php if (empty($posts)): ?>
        <p class="empty-state"><?= e($i18n->t('site.empty')) ?></p>
    <?php endif; ?>

    <?php foreach ($posts as $postItem): ?>
        <article class="post-card">
            <p class="post-meta"><?= e($postItem['date'] ?? '') ?></p>
            <h2><a href="<?= e($postItem['url']) ?>"><?= e($postItem['title'] ?? $postItem['slug']) ?></a></h2>
            <p><?= e($postItem['description'] ?? $postItem['excerpt'] ?? '') ?></p>
            <a class="read-more" href="<?= e($postItem['url']) ?>"><?= e($i18n->t('cta.read_more')) ?></a>
        </article>
    <?php endforeach; ?>
</section>
