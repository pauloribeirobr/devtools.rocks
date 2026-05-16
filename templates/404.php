<section class="page-heading">
    <!-- Mensagem 404 localizada pelo idioma corrente. -->
    <h1><?= e($i18n->t('error.404.title')) ?></h1>
    <p><?= e($i18n->t('error.404.message')) ?></p>
    <p><a href="/<?= e($i18n->getLocale()) ?>/"><?= e($i18n->t('nav.home')) ?></a></p>
</section>
