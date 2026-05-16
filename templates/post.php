<article class="post">
    <!-- Metadados principais do post vindos do frontmatter YAML. -->
    <header class="post-header">
        <p class="post-meta">
            <?= e($i18n->t('post.by_author')) ?> <?= e(AUTHOR_NAME) ?>
            <?php if (!empty($post['meta']['date'])): ?>
                · <?= e($i18n->t('post.published_on')) ?> <?= e($post['meta']['date']) ?>
            <?php endif; ?>
            <?php if (!empty($post['meta']['updated'])): ?>
                · <?= e($i18n->t('post.updated_on')) ?> <?= e($post['meta']['updated']) ?>
            <?php endif; ?>
        </p>
        <h1><?= e($post['meta']['title']) ?></h1>
        <?php if (!empty($post['meta']['description'])): ?>
            <p class="lead"><?= e($post['meta']['description']) ?></p>
        <?php endif; ?>
        <?php if (!empty($post['meta']['category'])): ?>
            <!-- Linka a categoria do post para reforcar o cluster interno. -->
            <p class="post-taxonomy">
                <?= e($i18n->t('post.in_category')) ?>
                <a href="/<?= e($i18n->getLocale()) ?>/<?= e($categoryRoute) ?>/<?= e($post['meta']['category']) ?>/">
                    <?= e($post['meta']['category_name'] ?? $post['meta']['category']) ?>
                </a>
            </p>
        <?php endif; ?>
    </header>

    <?php if (!empty($post['meta']['image'])): ?>
        <!-- Imagem principal declarada no frontmatter; as internas ficam no corpo Markdown. -->
        <figure class="post-cover">
            <img src="/content/<?= e($i18n->getLocale()) ?>/<?= e($post['meta']['slug']) ?>/<?= e($post['meta']['image']) ?>"
                 alt="<?= e($post['meta']['image_alt'] ?? $post['meta']['title']) ?>"
                 loading="eager">
        </figure>
    <?php endif; ?>

    <div class="post-content">
        <!-- HTML gerado pelo Parsedown; posts podem usar HTML inline controlado. -->
        <?= $post['html'] ?>
    </div>

    <?php if (!empty($post['meta']['tags'])): ?>
        <!-- Tags sao links de navegacao interna e ficam noindex na pagina de listagem. -->
        <footer class="post-tags">
            <strong><?= e($i18n->t('post.tags')) ?></strong>
            <div class="chips">
                <?php foreach ($post['meta']['tags'] as $tag): ?>
                    <a href="/<?= e($i18n->getLocale()) ?>/<?= e($tagRoute) ?>/<?= e($tag) ?>/"><?= e($tag) ?></a>
                <?php endforeach; ?>
            </div>
        </footer>
    <?php endif; ?>
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
