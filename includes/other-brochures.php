<?php
/**
 * Other Brochures listing page.
 */
require_once __DIR__ . '/functions.php';

$pageTitle = 'Other Brochures';
$pageDescription = 'Browse and download brochures from other product lines and brands.';
$currentPage = 'other-brochures';
$categories = catalog_get_other_brochure_categories();

require_once __DIR__ . '/header.php';
?>

<section class="page-hero page-hero--compact">
    <div class="container">
        <nav aria-label="Breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= CATALOG_BASE_PATH ?>">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Other Brochures</li>
            </ol>
        </nav>
        <h1 class="page-title">Other Brochures</h1>
        <p class="page-description">Browse and download brochures from other product lines and brands.</p>
    </div>
</section>

<section class="page-section">
    <div class="container">
        <?php if (empty($categories)): ?>
        <div class="catalog-empty">
            <i class="fa-regular fa-folder-open"></i>
            <p>No other brochures found yet.</p>
        </div>
        <?php else: ?>
        <div class="row g-4">
            <?php foreach ($categories as $cat): ?>
            <?php
                $desc = $cat['description'];
                $shortDesc = strlen($desc) > 100 ? substr($desc, 0, 100) . '...' : $desc;
            ?>
            <div class="col-md-6 col-lg-4">
                <a href="<?= htmlspecialchars($cat['url']) ?>" class="category-card">
                    <div class="category-card__icon"><i class="fa-solid fa-folder-open"></i></div>
                    <div class="category-card__body">
                        <h2 class="category-card__title"><?= htmlspecialchars($cat['name']) ?></h2>
                        <p class="category-card__desc"><?= htmlspecialchars($shortDesc) ?></p>
                        <span class="category-card__count"><?= (int) $cat['pdf_count'] ?> PDF<?= $cat['pdf_count'] !== 1 ? 's' : '' ?></span>
                    </div>
                </a>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</section>

<?php require_once __DIR__ . '/footer.php'; ?>
