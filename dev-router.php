<?php

declare(strict_types=1);

$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$file = __DIR__ . $path;

// O servidor embutido entrega arquivos reais, como CSS, sem passar pelo PHP.
if ($path !== '/' && is_file($file)) {
    return false;
}

// Rotas amigaveis caem no front controller.
require __DIR__ . '/index.php';
