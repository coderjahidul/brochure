<?php
/**
 * Contact page – form submits to contact@brochure.copiercatalog.com
 */
require_once __DIR__ . '/functions.php';

$pageTitle = 'Contact Us';
$pageDescription = 'Get in touch with Copier Catalog for questions about our PDF catalog library.';
$currentPage = 'contact';

$formResult = null;
$formValues = [
    'name' => '',
    'email' => '',
    'subject' => '',
    'message' => '',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $formValues = [
        'name' => trim((string) ($_POST['name'] ?? '')),
        'email' => trim((string) ($_POST['email'] ?? '')),
        'subject' => trim((string) ($_POST['subject'] ?? '')),
        'message' => trim((string) ($_POST['message'] ?? '')),
    ];
    $formResult = catalog_process_contact_form($_POST);

    if ($formResult['ok']) {
        $formValues = [
            'name' => '',
            'email' => '',
            'subject' => '',
            'message' => '',
        ];
    }
}

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
                        <li><i class="fa-solid fa-envelope"></i> <a href="mailto:<?= htmlspecialchars(CATALOG_CONTACT_EMAIL) ?>"><?= htmlspecialchars(CATALOG_CONTACT_EMAIL) ?></a></li>
                        <li><i class="fa-solid fa-phone"></i> <a href="tel:+19292426868">929-242-6868</a></li>
                    </ul>
                    <p class="text-muted mt-4">Business hours: Monday – Friday, 9:00 AM – 5:00 PM EST</p>
                </div>
            </div>
            <div class="col-lg-7">
                <div class="content-card">
                    <h2>Send a Message</h2>

                    <?php if ($formResult !== null): ?>
                        <div class="alert alert-<?= $formResult['ok'] ? 'success' : 'danger' ?>" role="alert">
                            <?= htmlspecialchars($formResult['message']) ?>
                        </div>
                    <?php endif; ?>

                    <form class="contact-form" action="<?= htmlspecialchars(catalog_url('contact/')) ?>" method="post" novalidate>
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(catalog_contact_csrf_token()) ?>">
                        <div class="visually-hidden" aria-hidden="true">
                            <label for="contact-website">Website</label>
                            <input type="text" id="contact-website" name="website" tabindex="-1" autocomplete="off">
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="contact-name" class="form-label">Name</label>
                                <input type="text" class="form-control<?= isset($formResult['field_errors']['name']) ? ' is-invalid' : '' ?>"
                                       id="contact-name" name="name" value="<?= htmlspecialchars($formValues['name']) ?>" required maxlength="100">
                                <?php if (isset($formResult['field_errors']['name'])): ?>
                                    <div class="invalid-feedback"><?= htmlspecialchars($formResult['field_errors']['name']) ?></div>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-6">
                                <label for="contact-email" class="form-label">Email</label>
                                <input type="email" class="form-control<?= isset($formResult['field_errors']['email']) ? ' is-invalid' : '' ?>"
                                       id="contact-email" name="email" value="<?= htmlspecialchars($formValues['email']) ?>" required>
                                <?php if (isset($formResult['field_errors']['email'])): ?>
                                    <div class="invalid-feedback"><?= htmlspecialchars($formResult['field_errors']['email']) ?></div>
                                <?php endif; ?>
                            </div>
                            <div class="col-12">
                                <label for="contact-subject" class="form-label">Subject</label>
                                <input type="text" class="form-control<?= isset($formResult['field_errors']['subject']) ? ' is-invalid' : '' ?>"
                                       id="contact-subject" name="subject" value="<?= htmlspecialchars($formValues['subject']) ?>" maxlength="200">
                                <?php if (isset($formResult['field_errors']['subject'])): ?>
                                    <div class="invalid-feedback"><?= htmlspecialchars($formResult['field_errors']['subject']) ?></div>
                                <?php endif; ?>
                            </div>
                            <div class="col-12">
                                <label for="contact-message" class="form-label">Message</label>
                                <textarea class="form-control<?= isset($formResult['field_errors']['message']) ? ' is-invalid' : '' ?>"
                                          id="contact-message" name="message" rows="5" required maxlength="5000"><?= htmlspecialchars($formValues['message']) ?></textarea>
                                <?php if (isset($formResult['field_errors']['message'])): ?>
                                    <div class="invalid-feedback"><?= htmlspecialchars($formResult['field_errors']['message']) ?></div>
                                <?php endif; ?>
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
