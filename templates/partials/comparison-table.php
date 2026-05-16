<?php if (!empty($post['meta']['affiliate'])): ?>
    <!-- Tabela padrao para comparativos e reviews com programas afiliados. -->
    <div class="comparison-table-wrap">
        <table class="comparison-table">
            <thead>
                <tr>
                    <th><?= e($i18n->t('table.product')) ?></th>
                    <th><?= e($i18n->t('table.rating')) ?></th>
                    <th><?= e($i18n->t('table.highlight')) ?></th>
                    <th><?= e($i18n->t('table.action')) ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($post['meta']['affiliate'] as $item): ?>
                    <?php
                    // Cada item precisa apontar para um programa configurado.
                    $program = $item['program'] ?? '';
                    if ($program === '' || !$affiliate->exists($program)) {
                        continue;
                    }
                    $rating = $item['rating'] ?? null;
                    ?>
                    <tr>
                        <td><?= e($item['name'] ?? $affiliate->name($program)) ?></td>
                        <td><?= $rating !== null ? e($rating . '/5') : '-' ?></td>
                        <td><?= e($item['highlight'] ?? '') ?></td>
                        <td>
                            <a class="btn-affiliate"
                               href="<?= e($affiliate->link($program, $post['meta']['slug'] ?? null)) ?>"
                               rel="<?= e($affiliate->rel($program)) ?>">
                                <?= e($i18n->t('cta.try_it')) ?>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>
