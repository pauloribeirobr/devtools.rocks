<?php

declare(strict_types=1);

return [
    // Idiomas suportados e slugs publicos traduzidos por idioma.
    'languages' => [
        'pt' => [
            'code' => 'pt',
            'locale' => 'pt-BR',
            'name' => 'Português',
            'flag' => 'PT',
            'url_slugs' => [
                'category' => 'categoria',
                'tag' => 'tag',
                'about' => 'sobre',
                'contact' => 'contato',
                'privacy' => 'privacidade',
            ],
        ],
        'en' => [
            'code' => 'en',
            'locale' => 'en-US',
            'name' => 'English',
            'flag' => 'EN',
            'url_slugs' => [
                'category' => 'category',
                'tag' => 'tag',
                'about' => 'about',
                'contact' => 'contact',
                'privacy' => 'privacy',
            ],
        ],
        'es' => [
            'code' => 'es',
            'locale' => 'es-ES',
            'name' => 'Español',
            'flag' => 'ES',
            'url_slugs' => [
                'category' => 'categoria',
                'tag' => 'tag',
                'about' => 'acerca',
                'contact' => 'contacto',
                'privacy' => 'privacidad',
            ],
        ],
    ],
    // Programas configurados para redirects internos em /go/{program}/.
    'affiliates' => [
        'hostinger' => [
            'url' => 'https://hostinger.com.br/afiliado/?ref=PAULORIBEIRO',
            'rel' => 'sponsored noopener',
            'name' => 'Hostinger',
        ],
        'digitalocean' => [
            'url' => 'https://m.do.co/c/PAULORIBEIRO',
            'rel' => 'sponsored noopener',
            'name' => 'DigitalOcean',
        ],
        'hostgator' => [
            'url' => 'https://hostgator.com.br/afiliado?ref=PAULORIBEIRO',
            'rel' => 'sponsored noopener',
            'name' => 'HostGator',
        ],
        'jetbrains' => [
            'url' => 'https://jetbrains.com/?ref=PAULORIBEIRO',
            'rel' => 'sponsored noopener',
            'name' => 'JetBrains',
        ],
        'datadog' => [
            'url' => 'https://datadoghq.com/?ref=PAULORIBEIRO',
            'rel' => 'sponsored noopener',
            'name' => 'Datadog',
        ],
    ],
];
