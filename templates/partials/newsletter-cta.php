<aside class="newsletter-cta" id="newsletter">
    <!-- CTA reutilizavel para capturar email ao fim de cada post. -->
    <h2><?= e($i18n->t('cta.newsletter.title')) ?></h2>
    <p><?= e($i18n->t('cta.newsletter.subtitle')) ?></p>
    <form method="post" action="<?= e(NEWSLETTER_FORM_ACTION !== '' ? NEWSLETTER_FORM_ACTION : '#newsletter') ?>">
        <label>
            <span class="sr-only">Email</span>
            <input type="email"
                   name="email"
                   placeholder="<?= e($i18n->t('cta.newsletter.email')) ?>"
                   autocomplete="email"
                   required>
        </label>
        <button type="submit"><?= e($i18n->t('cta.newsletter.button')) ?></button>
    </form>
</aside>
