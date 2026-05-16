<?php

declare(strict_types=1);

define('SITE_NAME', 'devtools.rocks');
define('SITE_TAGLINE', 'Dev tools e SaaS reviews para devs back-end');
define('SITE_URL', 'https://devtools.rocks');
define('SITE_EMAIL', 'contato@devtools.rocks');
define('SITE_DEFAULT_LANG', 'pt');
define('SITE_SUPPORTED_LANGS', ['pt', 'en', 'es']);

define('ROOT_PATH', dirname(__DIR__));
define('CONFIG_PATH', ROOT_PATH . '/config');
define('TEMPLATES_PATH', ROOT_PATH . '/templates');
define('CONTENT_PATH', ROOT_PATH . '/content');
define('LOCALES_PATH', ROOT_PATH . '/locales');
define('CACHE_PATH', ROOT_PATH . '/.cache');

define('CACHE_ENABLED', true);
define('CACHE_TTL', 3600);

define('OG_IMAGE', SITE_URL . '/assets/images/og-default.jpg');
define('AUTHOR_NAME', 'Paulo Ribeiro');
define('AUTHOR_URL', 'https://pauloribeiro.dev');

define('NEWSLETTER_PROVIDER', 'mailerlite');
define('NEWSLETTER_FORM_ID', 'XXXXX');

define('IS_PRODUCTION', getenv('APP_ENV') === 'production');
define('DEBUG_MODE', !IS_PRODUCTION);

date_default_timezone_set('America/Sao_Paulo');

// Garante que o cache em arquivo exista antes de qualquer leitura de conteudo.
if (!is_dir(CACHE_PATH)) {
    mkdir(CACHE_PATH, 0755, true);
}

// Sessao fica disponivel para futuras features sem rodar durante comandos CLI.
if (session_status() === PHP_SESSION_NONE && PHP_SAPI !== 'cli') {
    session_start();
}

// Em dev, mostra erros cedo; em producao, evita vazar detalhes tecnicos.
if (DEBUG_MODE) {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(0);
    ini_set('display_errors', '0');
}

if (!function_exists('e')) {
    // Escapa valores antes de imprimir HTML em templates.
    function e(mixed $value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}
