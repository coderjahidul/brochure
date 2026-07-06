<?php
/**
 * Homepage � displays latest PDFs per brand folder.
 */
$pageTitle = 'Free Copier Machine Brochures';
$pageDescription = 'Browse copier brochures from Canon, Epson, HP, Ricoh, Xerox, and other leading brands. View online or download for free - no registration required.';
$currentPage = 'home';

require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/header.php';

$categories = catalog_get_categories();
?>

<section class="hero-section">
    <div class="container">
        <div class="hero-content">
            <h1 class="hero-title">Free Copier Machine Brochures</h1>
            <p class="hero-subtitle">Browse copier brochures from Canon, Epson, HP, Ricoh, Xerox, and other leading brands. View online or download for free - no registration required.</p>
        </div>
    </div>
</section>

<?php if (empty($categories)): ?>
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
