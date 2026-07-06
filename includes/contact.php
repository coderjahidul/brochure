<?php
/**
 * Contact page – frontend only.
 */
require_once __DIR__ . '/functions.php';

$pageTitle = 'Contact Us';
$pageDescription = 'Get in touch with Copier Catalog for questions about our PDF catalog library.';
$currentPage = 'contact';

require_once __DIR__ . '/header.php';
?>

<section class="page-hero page-hero--compact">
    <div class="container">
        <nav aria-label="Breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= CATALOG_BASE_PATH ?>">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Contact</li>
            </ol>
        </nav>
        <h1 class="page-title">Contact Us</h1>
    </div>
</section>

<section class="page-section">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-5">
                <div class="content-card h-100">
                    <h2>Get In Touch</h2>
                    <ul class="footer-contact footer-contact--page">
                        <li><i class="fa-solid fa-location-dot"></i> 123 Fifth Avenue, New York, NY 10160</li>
                        <li><i class="fa-solid fa-envelope"></i> <a href="mailto:contact@info.com">contact@info.com</a></li>
                        <li><i class="fa-solid fa-phone"></i> <a href="tel:+19292426868">929-242-6868</a></li>
                    </ul>
                    <p class="text-muted mt-4">Business hours: Monday – Friday, 9:00 AM – 5:00 PM EST</p>
                </div>
            </div>
            <div class="col-lg-7">
                <div class="content-card">
                    <h2>Send a Message</h2>
                    <form class="contact-form" action="#" method="post" onsubmit="return false;">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="contact-name" class="form-label">Name</label>
                                <input type="text" class="form-control" id="contact-name" name="name" required>
                            </div>
                            <div class="col-md-6">
                                <label for="contact-email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="contact-email" name="email" required>
                            </div>
                            <div class="col-12">
                                <label for="contact-subject" class="form-label">Subject</label>
                                <input type="text" class="form-control" id="contact-subject" name="subject">
                            </div>
                            <div class="col-12">
                                <label for="contact-message" class="form-label">Message</label>
                                <textarea class="form-control" id="contact-message" name="message" rows="5" required></textarea>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-paper-plane"></i> Send Message</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/footer.php'; ?>
