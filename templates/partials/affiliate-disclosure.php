<?php if (!empty($post['meta']['affiliate'])): ?>
    <!-- Disclosure obrigatorio para posts com links de afiliado. -->
    <aside class="affiliate-disclosure">
        <p><?= e($i18n->t('disclosure.affiliate')) ?></p>
    </aside>
<?php endif; ?>
