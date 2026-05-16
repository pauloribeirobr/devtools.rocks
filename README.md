# devtools.rocks

Site PHP + Markdown para reviews, comparativos e tutoriais de ferramentas dev.

## Desenvolvimento local

```bash
composer install
php -S localhost:8000 dev-router.php
```

URLs principais:

- `/` redireciona para o idioma detectado ou `/pt/`
- `/pt/`, `/en/`, `/es/` exibem as homes por idioma
- `/sitemap.xml` gera sitemap XML
- `/robots.txt` aponta para o sitemap
