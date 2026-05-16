<?php

declare(strict_types=1);

namespace App;

class I18n
{
    private string $currentLang;

    private array $translations = [];

    public function __construct(private readonly array $languages, string $defaultLang)
    {
        $this->setLocale($defaultLang);
    }

    public function setLocale(string $lang): void
    {
        // Se o idioma nao existir, usa o primeiro idioma configurado.
        if (!isset($this->languages[$lang])) {
            $lang = array_key_first($this->languages);
        }

        $this->currentLang = $lang;
        $file = LOCALES_PATH . "/{$lang}.php";
        // Cada locale e um array PHP simples de chave => traducao.
        $this->translations = file_exists($file) ? require $file : [];
    }

    public function getLocale(): string
    {
        return $this->currentLang;
    }

    public function getLanguage(): array
    {
        return $this->languages[$this->currentLang];
    }

    public function t(string $key, array $vars = []): string
    {
        $value = $this->translations[$key] ?? $key;

        // Substitui placeholders como {category} nas strings traduzidas.
        foreach ($vars as $name => $replacement) {
            $value = str_replace('{' . $name . '}', (string) $replacement, $value);
        }

        return $value;
    }

    public function urlSlug(string $name): string
    {
        return $this->languages[$this->currentLang]['url_slugs'][$name] ?? $name;
    }

    public function detectFromBrowser(): string
    {
        $accept = $_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? '';

        // Faz uma deteccao simples por prefixo de idioma no Accept-Language.
        foreach (array_keys($this->languages) as $code) {
            if (preg_match('/(^|,\s*)' . preg_quote($code, '/') . '(-|;|,|$)/i', $accept)) {
                return $code;
            }
        }

        return SITE_DEFAULT_LANG;
    }

    public function allLanguages(): array
    {
        return $this->languages;
    }
}
