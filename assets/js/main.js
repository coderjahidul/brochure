/**
 * Copier Catalog � main JavaScript
 * Lazy loading, scroll-to-top, mobile enhancements
 */
(function () {
    'use strict';

    /* Back to top button */
    var scrollBtn = document.getElementById('scroll-to-top');
    if (scrollBtn) {
        var toggleScrollBtn = function () {
            scrollBtn.classList.toggle('visible', window.scrollY > 300);
        };
        window.addEventListener('scroll', toggleScrollBtn, { passive: true });
        toggleScrollBtn();

        scrollBtn.addEventListener('click', function () {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }

    /* Native lazy loading fallback for older browsers */
    if ('loading' in HTMLImageElement.prototype === false) {
        var lazyImages = document.querySelectorAll('img[loading="lazy"]');
        if ('IntersectionObserver' in window) {
            var observer = new IntersectionObserver(function (entries) {
                entries.forEach(function (entry) {
                    if (entry.isIntersecting) {
                        var img = entry.target;
                        if (img.dataset.src) {
                            img.src = img.dataset.src;
                        }
                        observer.unobserve(img);
                    }
                });
            });
            lazyImages.forEach(function (img) { observer.observe(img); });
        }
    }

    /* Close mobile nav on link click */
    var navCollapse = document.getElementById('mainNavbar');
    if (navCollapse && typeof bootstrap !== 'undefined') {
        navCollapse.querySelectorAll('.nav-link').forEach(function (link) {
            link.addEventListener('click', function () {
                var bsCollapse = bootstrap.Collapse.getInstance(navCollapse);
                if (bsCollapse && navCollapse.classList.contains('show')) {
                    bsCollapse.hide();
                }
            });
        });
    }

    /* Smooth scroll for anchor links */
    document.querySelectorAll('a[href^="#"]').forEach(function (anchor) {
        anchor.addEventListener('click', function (e) {
            var targetId = this.getAttribute('href');
            if (targetId.length <= 1) return;
            var target = document.querySelector(targetId);
            if (target) {
                e.preventDefault();
                target.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    });
})();
