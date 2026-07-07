<?php
/**
 * About page — frontend only.
 */
require_once __DIR__ . '/functions.php';

$pageTitle = 'About Us';
$pageDescription = 'Learn about Brochure Copier Catalog — your free destination for finding and downloading copier machine PDF brochures.';
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
        <h1 class="page-title">About Brochure Copier Catalog</h1>
    </div>
</section>

<section class="page-section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="content-card">
                    <p class="lead">Brochure Copier Catalog is a free online platform for finding and downloading Download Brochures in PDF format. Whether you're a copier dealer comparing models, a technician looking up specs, or an office manager researching your next purchase, our site makes it easy to find the exact brochure you need — organized by brand, with no account required and no complicated navigation.</p>

                    <h2>What We Offer</h2>
                    <ul>
                        <li>Copier machine brochures from major brands</li>
                        <li>Instant online viewing and one-click downloads</li>
                        <li>A clean, mobile-friendly design built for fast browsing on any device</li>
                        <li>No sign-up, subscription, or hidden fees</li>
                    </ul>

                    <h2>How It Works</h2>
                    <p>Simply select a copier brand, browse the available brochures, and view or download the PDF you need — instantly. Our copier catalog updates continuously, so new brochures appear on the site as soon as they're uploaded.</p>

                    <h2>Why Choose Brochure Copier Catalog</h2>
                    <p>Finding accurate, up-to-date copier specifications shouldn't be difficult. We built this site to save dealers, technicians, and buyers time by putting brochures from all the major copier brands together in one place, so you don't have to search multiple websites.</p>

                    <h2>From Our Blog</h2>
                    <p>Beyond our brochure library, our blog provides helpful guides, setup and maintenance tips, and insights on copiers and office equipment. It's a useful resource if you want more than just spec sheets — whether you're troubleshooting a printer issue or trying to get more out of your equipment.</p>

                    <div class="d-flex flex-wrap gap-2 mt-3">
                        <a href="<?= CATALOG_BASE_PATH ?>download-brochures/" class="btn btn-primary">Browse Download Brochures</a>
                        <a href="https://brochure.copiercatalog.com/blog/" class="btn btn-outline-primary">Visit Our Blog</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/footer.php'; ?>
