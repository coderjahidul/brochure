<?php
/**
 * Category page – all PDFs with search and pagination.
 */
require_once __DIR__ . '/functions.php';

$slug = isset($_GET['slug']) ? preg_replace('/[^a-z0-9-]/', '', strtolower($_GET['slug'])) : '';
$category = catalog_get_category($slug);

if ($category === null) {
    http_response_code(404);
    $pageTitle = 'Category Not Found';
    $currentPage = 'categories';
    require_once __DIR__ . '/header.php';
    echo '<section class="page-section"><div class="container">';
    echo '<div class="catalog-empty"><i class="fa-regular fa-face-frown"></i>';
    echo '<h1>Category Not Found</h1>';
    echo '<p>The category you are looking for does not exist.</p>';
    echo '<a href="' . CATALOG_BASE_PATH . '" class="btn btn-primary">Back to Home</a></div></div></section>';
    require_once __DIR__ . '/footer.php';
    exit;
}

$searchQuery = trim($_GET['q'] ?? '');
$page = max(1, (int) ($_GET['page'] ?? 1));
$filteredPdfs = catalog_filter_pdfs($category['pdfs'], $searchQuery);
$pagination = catalog_paginate($filteredPdfs, $page);

$pageTitle = $category['name'] . ' PDF Catalogs';
$pageDescription = $category['description'];
$currentPage = 'categories';
$baseUrl = $category['url'];
$extraParams = $searchQuery !== '' ? ['q' => $searchQuery] : [];

require_once __DIR__ . '/header.php';
?>

<section class="page-hero page-hero--compact">
    <div class="container">
        <nav aria-label="Breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= CATALOG_BASE_PATH ?>">Home</a></li>
                <li class="breadcrumb-item"><a href="<?= CATALOG_BASE_PATH ?>categories/">Categories</a></li>
                <li class="breadcrumb-item active" aria-current="page"><?= htmlspecialchars($category['name']) ?></li>
            </ol>
        </nav>
        <h1 class="page-title"><?= htmlspecialchars($category['name']) ?></h1>
        <p class="page-description"><?= htmlspecialchars($category['description']) ?></p>
    </div>
</section>

<section class="page-section">
    <div class="container">
        <div class="category-toolbar">
            <form class="category-search" method="get" action="<?= htmlspecialchars($category['url']) ?>" role="search">
                <div class="input-group">
                    <input type="search" class="form-control" name="q" placeholder="Search in <?= htmlspecialchars($category['name']) ?>…"
                           value="<?= htmlspecialchars($searchQuery) ?>" aria-label="Search PDFs in category">
                    <button class="btn btn-primary" type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
                </div>
            </form>
            <p class="category-count text-muted mb-0">
                <?= (int) $pagination['total'] ?> catalog<?= $pagination['total'] !== 1 ? 's' : '' ?> found
            </p>
        </div>

        <?= catalog_render_pdf_grid($pagination['items']) ?>
        <?= catalog_render_pagination($pagination['page'], $pagination['total_pages'], $baseUrl, $extraParams) ?>
    </div>
</section>

<?php require_once __DIR__ . '/footer.php'; ?>
