<?php
/**
 * Site footer ÃƒÂ¯Ã‚Â¿Ã‚Â½ 4-column layout, social links, back to top.
 */
$basePath = CATALOG_BASE_PATH;
$categories = catalog_get_categories();
?>
</main>

<footer class="site-footer" id="colophon">
    <div class="footer-main">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-3 col-md-6">
                    <div class="footer-brand">
                        <img src="<?= $basePath ?>assets/images/icon.png" alt="" class="footer-brand__icon" width="64" height="64" loading="lazy">
                        <p class="footer-brand__desc">Brochure Copier Catalog is a free online platform for finding and downloading copier brochures in PDF format. Whether you're a copier dealer comparing models, a technician looking up specs, or an office manager researching your next purchase, our site makes it easy to find the exact brochure you need — organized by brand. <a href="<?= $basePath ?>about/">Read more</a></p>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6">
                    <h5 class="footer-heading">Copier Brochures</h5>
                    <ul class="footer-links">
                        <?php foreach (array_slice($categories, 0, 6) as $cat): ?>
                        <li><a href="<?= htmlspecialchars($cat['url']) ?>"><?= htmlspecialchars($cat['name']) ?></a></li>
                        <?php endforeach; ?>
                        <?php if (count($categories) > 8): ?>
                        <li><a href="<?= $basePath ?>copier-brochures/">View all Copier Brochures</a></li>
                        <?php endif; ?>
                    </ul>
                </div>

                <div class="col-lg-3 col-md-6">
                    <h5 class="footer-heading">Quick Links</h5>
                    <ul class="footer-links">
                        <li><a href="<?= $basePath ?>">Home</a></li>
                        <li><a href="<?= $basePath ?>copier-brochures/">Copier Brochures</a></li>
                        <li><a href="<?= $basePath ?>search/">Search Catalogs</a></li>
                        <li><a href="<?= $basePath ?>about/">About Us</a></li>
                        <li><a href="https://brochure.copiercatalog.com/blog/">Blog</a></li>
                        <li><a href="<?= $basePath ?>contact/">Contact</a></li>
                    </ul>
                </div>

                <div class="col-lg-3 col-md-6">
                    <h5 class="footer-heading">Contact</h5>
                    <ul class="footer-contact">
                        <li><i class="fa-solid fa-location-dot"></i> 123 Fifth Avenue, New York, NY 10160</li>
                        <li><i class="fa-solid fa-envelope"></i> <a href="mailto:<?= htmlspecialchars(CATALOG_CONTACT_EMAIL) ?>"><?= htmlspecialchars(CATALOG_CONTACT_EMAIL) ?></a></li>
                        <li><i class="fa-solid fa-phone"></i> <a href="tel:+19292426868">929-242-6868</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="footer-bottom">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6 text-center text-md-start">
                    <p class="footer-copyright mb-0">&copy; <?= date('Y') ?> Brochure Copier Catalog. All rights reserved.</p>
                </div>
                <div class="col-md-6 text-center text-md-end">
                    <div class="footer-social">
                        <a href="#" aria-label="Facebook"><i class="fa-brands fa-facebook-f"></i></a>
                        <a href="#" aria-label="Twitter"><i class="fa-brands fa-x-twitter"></i></a>
                        <a href="#" aria-label="LinkedIn"><i class="fa-brands fa-linkedin-in"></i></a>
                        <a href="#" aria-label="YouTube"><i class="fa-brands fa-youtube"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>

<button type="button" class="scroll-to-top" id="scroll-to-top" aria-label="Back to top">
    <i class="fa-solid fa-arrow-up"></i>
</button>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<script src="<?= $basePath ?>assets/js/main.js" defer></script>
</body>
</html>
