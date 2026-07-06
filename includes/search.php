<?php
/**
 * Global search page – searches PDF titles and category names.
 */
require_once __DIR__ . '/functions.php';

$query = trim($_GET['q'] ?? '');
$page = max(1, (int) ($_GET['page'] ?? 1));

$results = catalog_search($query);
$pagination = catalog_paginate($results['pdfs'], $page, CATALOG_PER_PAGE);

$pageTitle = $query !== '' ? 'Search: ' . $query : 'Search Catalogs';
$pageDescription = 'Search copier machine PDF catalogs by brand or product name.';
$currentPage = 'search';

require_once __DIR__ . '/header.php';
?>

<section class="page-hero page-hero--compact">
    <div class="container">
        <nav aria-label="Breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= CATALOG_BASE_PATH ?>">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Search</li>
            </ol>
        </nav>
        <h1 class="page-title">Search Catalogs</h1>
    </div>
</section>

<section class="page-section">
    <div class="container">
        <form class="search-page-form mb-4" method="get" action="<?= CATALOG_BASE_PATH ?>search/" role="search">
            <div class="input-group input-group-lg">
                <input type="search" class="form-control" name="q" placeholder="Search by PDF title or category…"
                       value="<?= htmlspecialchars($query) ?>" aria-label="Search query" required>
                <button class="btn btn-primary" type="submit"><i class="fa-solid fa-magnifying-glass"></i> Search</button>
            </div>
        </form>

        <?php if ($query === ''): ?>
        <div class="catalog-empty catalog-empty--sm">
            <i class="fa-solid fa-magnifying-glass"></i>
            <p>Enter a keyword to search PDF catalogs and categories.</p>
        </div>

        <?php else: ?>

        <?php if (!empty($results['categories'])): ?>
        <div class="search-categories mb-5">
            <h2 class="search-section-title">Matching Categories</h2>
            <div class="row g-3">
                <?php foreach ($results['categories'] as $cat): ?>
                <div class="col-md-4 col-sm-6">
                    <a href="<?= htmlspecialchars($cat['url']) ?>" class="category-chip">
                        <i class="fa-solid fa-folder"></i>
                        <span><?= htmlspecialchars($cat['name']) ?></span>
                        <small><?= (int) $cat['pdf_count'] ?> PDFs</small>
                    </a>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <div class="search-results">
            <h2 class="search-section-title">
                Matching PDFs
                <span class="text-muted fs-6">(<?= (int) $pagination['total'] ?>)</span>
            </h2>

            <?php if (empty($pagination['items'])): ?>
            <div class="catalog-empty catalog-empty--sm">
                <p>No PDF catalogs matched your search.</p>
            </div>
            <?php else: ?>
            <?= catalog_render_pdf_grid($pagination['items']) ?>
            <?= catalog_render_pagination(
                $pagination['page'],
                $pagination['total_pages'],
                CATALOG_BASE_PATH . 'search/',
                ['q' => $query]
            ) ?>
            <?php endif; ?>
        </div>

        <?php endif; ?>
    </div>
</section>

<?php require_once __DIR__ . '/footer.php'; ?>
