<?php

declare(strict_types=1);

require __DIR__ . '/config/config.php';
require __DIR__ . '/vendor/autoload.php';

use App\ContentManager;
use App\AffiliateLinker;
use App\I18n;
use App\SeoHelper;

$cfg = require CONFIG_PATH . '/languages.php';
$languages = $cfg['languages'];
$affiliates = $cfg['affiliates'];

// Objetos compartilhados pelas rotas desta requisicao.
$i18n = new I18n($languages, SITE_DEFAULT_LANG);
$affiliate = new AffiliateLinker($affiliates);
$requestUri = trim(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/', '/');

// Variaveis padrao lidas pelo header; cada rota especializa o que precisar.
$hreflang_tags = '';
$schema_page = '';
$schema_article = '';
$schema_faq = '';
$schema_breadcrumb = '';
$breadcrumbItems = [];
$og_type = 'website';

// Sitemap e global porque agrega posts de todos os idiomas.
if ($requestUri === 'sitemap.xml') {
    header('Content-Type: application/xml; charset=UTF-8');
    $allPosts = [];

    foreach (array_keys($languages) as $languageCode) {
        $allPosts[$languageCode] = (new ContentManager($languageCode))->getAllPosts();
    }

    require TEMPLATES_PATH . '/sitemap.php';
    exit;
}

// Robots tambem e global e aponta crawlers para o sitemap unificado.
if ($requestUri === 'robots.txt') {
    header('Content-Type: text/plain; charset=UTF-8');
    echo "User-agent: *\n";
    echo "Allow: /\n\n";
    echo 'Sitemap: ' . SITE_URL . "/sitemap.xml\n";
    exit;
}

// Redirect de afiliado: registra clique e responde 302 para o parceiro.
if (preg_match('#^go/([a-z0-9-]+)/?$#', $requestUri, $matches)) {
    $affiliate->redirect($matches[1], $_GET['ref'] ?? null);
}

// A raiz canonica redireciona para o idioma detectado ou o default.
if ($requestUri === '') {
    $lang = $i18n->detectFromBrowser();
    header("Location: /{$lang}/", true, 302);
    exit;
}

// Todas as demais rotas publicas precisam comecar com /pt, /en ou /es.
if (!preg_match('#^(' . implode('|', array_keys($languages)) . ')(/.*)?$#', $requestUri, $matches)) {
    http_response_code(404);
    renderNotFound($i18n, SITE_DEFAULT_LANG);
}

$lang = $matches[1];
$rest = trim($matches[2] ?? '', '/');
$i18n->setLocale($lang);
$manager = new ContentManager($lang);
$seo = new SeoHelper($lang, $languages);
$categoryRoute = $i18n->urlSlug('category');
$tagRoute = $i18n->urlSlug('tag');

// Home do idioma: lista posts publicados e categorias derivadas do conteudo.
if ($rest === '') {
    $posts = $manager->getAllPosts();
    $categories = $manager->getCategories();
    $page_title = SITE_NAME . ' - ' . SITE_TAGLINE;
    $meta_description = $i18n->t('site.meta_description');
    $canonical = SITE_URL . "/{$lang}/";
    $og_image = OG_IMAGE;
    $hreflang_tags = $seo->localizedHomeHreflangTags();
    $schema_page = $seo->webSiteSchema()
        . "\n"
        . $seo->webPageSchema($canonical, $page_title, $meta_description, 'WebPage');

    require TEMPLATES_PATH . '/header.php';
    require TEMPLATES_PATH . '/home.php';
    require TEMPLATES_PATH . '/footer.php';
    exit;
}

// Listagem por categoria com slug traduzido por idioma.
if (preg_match("#^{$categoryRoute}/([a-z0-9-]+)/?$#", $rest, $matches)) {
    $categorySlug = $matches[1];
    $posts = $manager->getPostsByCategory($categorySlug);
    $categories = $manager->getCategories();
    $categoryName = $categories[$categorySlug] ?? $categorySlug;
    $page_title = $i18n->t('category.title', ['category' => $categoryName]) . ' - ' . SITE_NAME;
    $meta_description = $i18n->t('category.meta_description', ['category' => $categoryName]);
    $canonical = SITE_URL . "/{$lang}/{$categoryRoute}/{$categorySlug}/";
    $og_image = OG_IMAGE;
    $hreflang_tags = $seo->localizedTaxonomyHreflangTags('category', $categorySlug);
    $breadcrumbItems = [
        ['label' => $i18n->t('nav.home'), 'url' => SITE_URL . "/{$lang}/"],
        ['label' => $categoryName],
    ];
    $schema_breadcrumb = $seo->breadcrumbSchema($breadcrumbItems);
    $schema_page = $seo->collectionPageSchema($canonical, $page_title, $meta_description, $posts);

    require TEMPLATES_PATH . '/header.php';
    require TEMPLATES_PATH . '/category.php';
    require TEMPLATES_PATH . '/footer.php';
    exit;
}

// Listagem por tag fica noindex para evitar paginas finas indexadas cedo.
if (preg_match("#^{$tagRoute}/([a-z0-9-]+)/?$#", $rest, $matches)) {
    $tagName = $matches[1];
    $posts = $manager->getPostsByTag($tagName);
    $page_title = $i18n->t('tag.title', ['tag' => $tagName]) . ' - ' . SITE_NAME;
    $meta_description = $i18n->t('tag.meta_description', ['tag' => $tagName]);
    $canonical = SITE_URL . "/{$lang}/{$tagRoute}/{$tagName}/";
    $og_image = OG_IMAGE;
    $meta_robots = 'noindex, follow';
    $hreflang_tags = $seo->localizedTaxonomyHreflangTags('tag', $tagName);
    $breadcrumbItems = [
        ['label' => $i18n->t('nav.home'), 'url' => SITE_URL . "/{$lang}/"],
        ['label' => $tagName],
    ];
    $schema_breadcrumb = $seo->breadcrumbSchema($breadcrumbItems);
    $schema_page = $seo->collectionPageSchema($canonical, $page_title, $meta_description, $posts);

    require TEMPLATES_PATH . '/header.php';
    require TEMPLATES_PATH . '/tag.php';
    require TEMPLATES_PATH . '/footer.php';
    exit;
}

// Paginas estaticas basicas usam os slugs configurados por idioma.
foreach (['about', 'contact', 'privacy'] as $staticPage) {
    if ($rest === $i18n->urlSlug($staticPage)) {
        $page_title = $i18n->t("page.{$staticPage}.title") . ' - ' . SITE_NAME;
        $meta_description = $i18n->t("page.{$staticPage}.meta_description");
        $canonical = SITE_URL . "/{$lang}/{$rest}/";
        $og_image = OG_IMAGE;
        $hreflang_tags = $seo->localizedStaticHreflangTags($staticPage);
        $breadcrumbItems = [
            ['label' => $i18n->t('nav.home'), 'url' => SITE_URL . "/{$lang}/"],
            ['label' => $i18n->t("page.{$staticPage}.title")],
        ];
        $schema_breadcrumb = $seo->breadcrumbSchema($breadcrumbItems);
        $pageSchemaType = match ($staticPage) {
            'about' => 'AboutPage',
            'contact' => 'ContactPage',
            'privacy' => 'PrivacyPolicy',
        };
        $schema_page = $seo->webPageSchema($canonical, $page_title, $meta_description, $pageSchemaType);

        require TEMPLATES_PATH . '/header.php';
        require TEMPLATES_PATH . "/{$staticPage}.php";
        require TEMPLATES_PATH . '/footer.php';
        exit;
    }
}

// Post individual: /{lang}/{slug}/.
if (preg_match('#^([a-z0-9-]+)/?$#', $rest, $matches)) {
    $slug = $matches[1];
    $post = $manager->getPost($slug);

    if ($post === null) {
        http_response_code(404);
        renderNotFound($i18n, $lang);
    }

    $page_title = $post['meta']['title'] . ' - ' . SITE_NAME;
    $meta_description = $post['meta']['description']
        ?? trim(substr(preg_replace('/\s+/', ' ', strip_tags($post['html'])) ?? '', 0, 155));
    $canonical = SITE_URL . "/{$lang}/{$slug}/";
    $og_image = !empty($post['meta']['image'])
        ? SITE_URL . "/content/{$lang}/{$slug}/{$post['meta']['image']}"
        : OG_IMAGE;
    $og_type = 'article';
    $hreflang_tags = $seo->postHreflangTags($post['meta']);
    $breadcrumbItems = [
        ['label' => $i18n->t('nav.home'), 'url' => SITE_URL . "/{$lang}/"],
    ];
    if (!empty($post['meta']['category'])) {
        $categories = $manager->getCategories();
        $categoryName = $post['meta']['category_name'] ?? $categories[$post['meta']['category']] ?? $post['meta']['category'];
        $breadcrumbItems[] = [
            'label' => $categoryName,
            'url' => SITE_URL . "/{$lang}/{$categoryRoute}/{$post['meta']['category']}/",
        ];
    }
    $breadcrumbItems[] = ['label' => $post['meta']['title']];
    $schema_breadcrumb = $seo->breadcrumbSchema($breadcrumbItems);
    $schema_article = $seo->schemaByType($post, $canonical);
    $schema_faq = $seo->faqSchema($post);
    $related = $manager->getRelatedPosts($slug, $post['meta']['category'] ?? '');

    require TEMPLATES_PATH . '/header.php';
    require TEMPLATES_PATH . '/post.php';
    require TEMPLATES_PATH . '/footer.php';
    exit;
}

http_response_code(404);
renderNotFound($i18n, $lang);

function renderNotFound(I18n $i18n, string $lang): never
{
    // Renderiza 404 com locale correto e headers de noindex.
    $i18n->setLocale($lang);
    $page_title = $i18n->t('error.404.title') . ' - ' . SITE_NAME;
    $meta_description = $i18n->t('error.404.message');
    $canonical = SITE_URL . "/{$lang}/";
    $og_image = OG_IMAGE;
    $meta_robots = 'noindex, follow';
    $og_type = 'website';

    require TEMPLATES_PATH . '/header.php';
    require TEMPLATES_PATH . '/404.php';
    require TEMPLATES_PATH . '/footer.php';
    exit;
}
