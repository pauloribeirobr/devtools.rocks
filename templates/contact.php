<section class="page-heading">
    <!-- Contato inicial simples; pode evoluir para formulario depois. -->
    <h1><?= e($i18n->t('page.contact.title')) ?></h1>
    <p><?= e($i18n->t('page.contact.meta_description')) ?></p>
</section>

<section class="prose">
    <p><a href="mailto:<?= e(SITE_EMAIL) ?>"><?= e(SITE_EMAIL) ?></a></p>
</section>
