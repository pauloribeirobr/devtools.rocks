<header class="page-heading">
    <!-- Cabecalho da taxonomia com nome derivado dos metadados dos posts. -->
    <h1><?= e($i18n->t('category.title', ['category' => $categoryName])) ?></h1>
    <p><?= e($meta_description) ?></p>
</header>

<section class="post-list" aria-label="Posts">
    <!-- Lista todos os posts publicados da categoria atual. -->
    <?php if (empty($posts)): ?>
        <p class="empty-state"><?= e($i18n->t('site.empty')) ?></p>
    <?php endif; ?>

    <?php foreach ($posts as $postItem): ?>
        <article class="post-card">
            <p class="post-meta"><?= e($postItem['date'] ?? '') ?></p>
            <h2><a href="<?= e($postItem['url']) ?>"><?= e($postItem['title'] ?? $postItem['slug']) ?></a></h2>
            <p><?= e($postItem['description'] ?? $postItem['excerpt'] ?? '') ?></p>
        </article>
    <?php endforeach; ?>
</section>
