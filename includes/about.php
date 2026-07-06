<?php
/**
 * About page � frontend only.
 */
require_once __DIR__ . '/functions.php';

$pageTitle = 'About Us';
$pageDescription = 'Learn about Copier Catalog � your destination for copier machine PDF brochures.';
$currentPage = 'about';

require_once __DIR__ . '/header.php';
?>

<section class="page-hero page-hero--compact">
    <div class="container">
        <nav aria-label="Breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= CATALOG_BASE_PATH ?>">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">About</li>
            </ol>
        </nav>
        <h1 class="page-title">About Copier Catalog</h1>
    </div>
</section>

<section class="page-section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="content-card">
                    <p class="lead">Copier Catalog is a free online resource for browsing and downloading copier machine product brochures in PDF format.</p>
                    <p>We organize catalogs by brand so dealers, technicians, and office managers can quickly find the specifications and features they need � without creating an account or navigating complex portals.</p>
                    <h2>What We Offer</h2>
                    <ul>
                        <li>PDF catalogs from major copier brands</li>
                        <li>Instant online viewing and download</li>
                        <li>Organized categories with search</li>
                        <li>Mobile-friendly, fast-loading pages</li>
                    </ul>
                    <h2>How It Works</h2>
                    <p>Catalogs are automatically loaded from our document library. When new PDFs are added to a brand folder, they appear on the site immediately � no manual updates required.</p>
                    <a href="<?= CATALOG_BASE_PATH ?>copier-brochures/" class="btn btn-primary mt-3">Browse Copier Brochures</a>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/footer.php'; ?>
