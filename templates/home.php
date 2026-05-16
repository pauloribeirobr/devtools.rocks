<section class="page-hero">
    <!-- Home do idioma atual: posiciona o site e lista conteudo publicado. -->
    <p class="eyebrow"><?= e(SITE_NAME) ?></p>
    <h1><?= e(SITE_TAGLINE) ?></h1>
    <p><?= e($i18n->t('site.meta_description')) ?></p>
</section>

<?php if (!empty($categories)): ?>
    <!-- Categorias sao derivadas dos posts publicados, sem cadastro manual. -->
    <section class="taxonomy-list" aria-labelledby="categories-title">
        <h2 id="categories-title"><?= e($i18n->t('site.categories')) ?></h2>
        <div class="chips">
            <?php foreach ($categories as $slug => $name): ?>
                <a href="/<?= e($i18n->getLocale()) ?>/<?= e($i18n->urlSlug('category')) ?>/<?= e($slug) ?>/"><?= e($name) ?></a>
            <?php endforeach; ?>
        </div>
    </section>
<?php endif; ?>

<section class="post-list" aria-label="Posts">
    <!-- Estado vazio e esperado antes do primeiro post publicado. -->
    <?php if (empty($posts)): ?>
        <p class="empty-state"><?= e($i18n->t('site.empty')) ?></p>
    <?php endif; ?>

    <?php foreach ($posts as $postItem): ?>
        <article class="post-card">
            <p class="post-meta"><?= e($postItem['date'] ?? '') ?></p>
            <h2><a href="<?= e($postItem['url']) ?>"><?= e($postItem['title'] ?? $postItem['slug']) ?></a></h2>
            <?php if (!empty($postItem['description'])): ?>
                <p><?= e($postItem['description']) ?></p>
            <?php elseif (!empty($postItem['excerpt'])): ?>
                <p><?= e($postItem['excerpt']) ?></p>
            <?php endif; ?>
            <a class="read-more" href="<?= e($postItem['url']) ?>"><?= e($i18n->t('cta.read_more')) ?></a>
        </article>
    <?php endforeach; ?>
</section>
