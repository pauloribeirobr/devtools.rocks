<?php

declare(strict_types=1);

namespace App;

class AffiliateLinker
{
    public function __construct(private readonly array $programs)
    {
    }

    public function exists(string $program): bool
    {
        // Slugs de programas precisam existir no config para evitar redirects arbitrarios.
        return isset($this->programs[$program]);
    }

    public function link(string $program, ?string $ref = null): string
    {
        // Posts apontam para esta URL interna, nao para o link sujo do afiliado.
        $url = '/go/' . rawurlencode($program) . '/';

        if ($ref !== null && $ref !== '') {
            $url .= '?ref=' . rawurlencode($ref);
        }

        return $url;
    }

    public function rel(string $program): string
    {
        // rel sponsored deixa a intencao comercial explicita para buscadores.
        return $this->programs[$program]['rel'] ?? 'sponsored noopener';
    }

    public function name(string $program): string
    {
        return $this->programs[$program]['name'] ?? $program;
    }

    public function redirect(string $program, ?string $ref = null): never
    {
        if (!$this->exists($program)) {
            http_response_code(404);
            header('Content-Type: text/plain; charset=UTF-8');
            echo 'Affiliate program not found.';
            exit;
        }

        $this->logClick($program, $ref);

        header('X-Robots-Tag: noindex, nofollow', true);
        header('Location: ' . $this->programs[$program]['url'], true, 302);
        exit;
    }

    private function logClick(string $program, ?string $ref): void
    {
        if (!is_dir(CACHE_PATH)) {
            mkdir(CACHE_PATH, 0755, true);
        }

        $file = CACHE_PATH . '/affiliate-log.csv';
        $shouldWriteHeader = !file_exists($file) || filesize($file) === 0;

        // O IP e hasheado para medir cliques sem armazenar endereco bruto.
        $row = [
            date('c'),
            $program,
            $ref ?? '',
            hash('sha256', $_SERVER['REMOTE_ADDR'] ?? ''),
            $_SERVER['HTTP_USER_AGENT'] ?? '',
        ];

        $csv = '';
        if ($shouldWriteHeader) {
            $csv .= $this->csvLine(['clicked_at', 'program', 'ref', 'ip_hash', 'user_agent']);
        }
        $csv .= $this->csvLine($row);

        @file_put_contents($file, $csv, FILE_APPEND | LOCK_EX);
    }

    private function csvLine(array $values): string
    {
        // Usa fputcsv para escapar corretamente virgulas e aspas.
        $handle = fopen('php://temp', 'r+');
        if ($handle === false) {
            return '';
        }

        fputcsv($handle, $values);
        rewind($handle);
        $line = stream_get_contents($handle) ?: '';
        fclose($handle);

        return $line;
    }
}
