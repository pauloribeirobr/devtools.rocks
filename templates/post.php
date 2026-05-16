<article class="post">
    <!-- Metadados principais do post vindos do frontmatter YAML. -->
    <header class="post-header">
        <p class="post-meta">
            <?= e($i18n->t('post.by_author')) ?> <?= e(AUTHOR_NAME) ?>
            <?php if (!empty($post['meta']['date'])): ?>
                · <?= e($i18n->t('post.published_on')) ?> <?= e($post['meta']['date']) ?>
            <?php endif; ?>
        </p>
        <h1><?= e($post['meta']['title']) ?></h1>
        <?php if (!empty($post['meta']['description'])): ?>
            <p class="lead"><?= e($post['meta']['description']) ?></p>
        <?php endif; ?>
    </header>

    <div class="post-content">
        <!-- HTML gerado pelo Parsedown; posts podem usar HTML inline controlado. -->
        <?= $post['html'] ?>
    </div>
</article>

<?php if (!empty($related)): ?>
    <!-- Relacionados sao limitados a posts da mesma categoria. -->
    <section class="related-posts" aria-labelledby="related-title">
        <h2 id="related-title"><?= e($i18n->t('post.related')) ?></h2>
        <?php foreach ($related as $postItem): ?>
            <article>
                <h3><a href="<?= e($postItem['url']) ?>"><?= e($postItem['title'] ?? $postItem['slug']) ?></a></h3>
            </article>
        <?php endforeach; ?>
    </section>
<?php endif; ?>
