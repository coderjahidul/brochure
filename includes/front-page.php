<?php
/**
 * Homepage – displays latest PDFs per brand folder, or search results when ?q= is set.
 */
require_once __DIR__ . '/functions.php';

$query = trim($_GET['q'] ?? '');
$pageNum = max(1, (int) ($_GET['page'] ?? 1));
$isSearch = $query !== '';

if ($isSearch) {
    $results = catalog_search($query);
    $pagination = catalog_paginate($results['pdfs'], $pageNum, CATALOG_PER_PAGE);
    $pageTitle = 'Search: ' . $query;
    $pageDescription = 'Search results for "' . $query . '" in copier machine PDF catalogs.';
} else {
    $pageTitle = 'Free Copier Machine Brochures – Download PDFs';
    $pageDescription = 'Download free copier brochures from Canon, HP, Ricoh, Xerox & more. Instant PDF access, no registration required.';
}

$currentPage = 'home';
require_once __DIR__ . '/header.php';

$categories = catalog_get_categories();
?>

<section class="hero-section">
    <div class="container">
        <div class="hero-content">
            <?php if ($isSearch): ?>
            <h1 class="hero-title">Search Results</h1>
            <p class="hero-subtitle">Showing results for &ldquo;<?= htmlspecialchars($query) ?>&rdquo;</p>
            <?php else: ?>
            <h1 class="hero-title">Download Copier Machine Brochure PDFs Instantly — 100% Free</h1>
            <p class="hero-subtitle">Explore and instantly download copier machine brochure PDFs from Canon, Epson, HP, Ricoh, Xerox & other leading brands — 100% free, no registration required.</p>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php if ($isSearch): ?>

<section class="page-section">
    <div class="container">
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
                CATALOG_BASE_PATH,
                ['q' => $query]
            ) ?>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php elseif (empty($categories)): ?>

<section class="page-section">
    <div class="container">
        <div class="catalog-empty">
            <i class="fa-regular fa-folder-open"></i>
            <h2>No categories found</h2>
            <p>Add brand folders with PDF files in the site root (e.g. <code>canon/</code>, <code>ricoh/</code>) to get started.</p>
        </div>
    </div>
</section>

<?php else: ?>

<?php foreach ($categories as $category): ?>
<?php
    $pdfs = array_slice($category['pdfs'], 0, CATALOG_HOMEPAGE_LIMIT);
    $hasMore = $category['pdf_count'] > CATALOG_HOMEPAGE_LIMIT;
?>
<section class="category-section page-section" id="category-<?= htmlspecialchars($category['slug']) ?>">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title"><?= htmlspecialchars($category['name']) ?></h2>
            <span class="section-badge"><?= (int) $category['pdf_count'] ?> PDF<?= $category['pdf_count'] !== 1 ? 's' : '' ?></span>
        </div>

        <?php if (empty($pdfs)): ?>
        <div class="catalog-empty catalog-empty--sm">
            <p>No PDF files in this category yet.</p>
        </div>
        <?php else: ?>
        <?= catalog_render_pdf_grid($pdfs) ?>

        <?php if ($hasMore): ?>
        <div class="section-footer text-center">
            <a href="<?= htmlspecialchars($category['url']) ?>" class="btn btn-outline-primary btn-view-more">
                View More <i class="fa-solid fa-arrow-right"></i>
            </a>
        </div>
        <?php endif; ?>
        <?php endif; ?>
    </div>
</section>
<?php endforeach; ?>

<?php endif; ?>

<?php require_once __DIR__ . '/footer.php'; ?>
