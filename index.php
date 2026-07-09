<?php
/**
 * Front controller – routes SEO-friendly URLs to page templates.
 */
require_once __DIR__ . '/includes/functions.php';

$route = $_GET['page'] ?? 'home';
if (is_numeric($route)) {
    $route = 'home';
}

switch ($route) {
    case 'category':
        require __DIR__ . '/includes/category.php';
        break;

    case 'download-copier-brochures':
        require __DIR__ . '/includes/categories.php';
        break;

    case 'other-brochures':
        require __DIR__ . '/includes/other-brochures.php';
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
