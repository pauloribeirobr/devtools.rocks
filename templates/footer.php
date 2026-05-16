    </main>
    <!-- Rodape simples com links institucionais e autoria. -->
    <footer class="site-footer">
        <p>&copy; <?= date('Y') ?> <?= e(SITE_NAME) ?>.</p>
        <nav aria-label="Footer">
            <a href="/<?= e($i18n->getLocale()) ?>/<?= e($i18n->urlSlug('privacy')) ?>/"><?= e($i18n->t('nav.privacy')) ?></a>
            <a href="<?= e(AUTHOR_URL) ?>" rel="author">pauloribeiro.dev</a>
        </nav>
    </footer>
</body>
</html>
