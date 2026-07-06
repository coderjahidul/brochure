<?php
/**
 * Front controller – routes SEO-friendly URLs to page templates.
 */
require_once __DIR__ . '/includes/functions.php';

$page = $_GET['page'] ?? 'home';

switch ($page) {
    case 'category':
        require __DIR__ . '/includes/category.php';
        break;

    case 'search':
        require __DIR__ . '/includes/search.php';
        break;

    case 'copier-brochures':
        require __DIR__ . '/includes/categories.php';
        break;

    case 'about':
        require __DIR__ . '/includes/about.php';
        break;

    case 'contact':
        require __DIR__ . '/includes/contact.php';
        break;

    default:
        require __DIR__ . '/includes/front-page.php';
        break;
}
