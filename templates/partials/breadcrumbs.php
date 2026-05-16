<!-- Breadcrumb visual sincronizado com o BreadcrumbList JSON-LD. -->
<nav class="breadcrumbs" aria-label="Breadcrumb">
    <ol>
        <?php foreach ($breadcrumbItems as $index => $item): ?>
            <li>
                <?php if (!empty($item['url']) && $index < count($breadcrumbItems) - 1): ?>
                    <a href="<?= e($item['url']) ?>"><?= e($item['label']) ?></a>
                <?php else: ?>
                    <span aria-current="page"><?= e($item['label']) ?></span>
                <?php endif; ?>
            </li>
        <?php endforeach; ?>
    </ol>
</nav>
