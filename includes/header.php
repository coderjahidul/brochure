<?php
/**
 * Site header ÃƒÂ¯Ã‚Â¿Ã‚Â½ sticky navigation, search, mobile menu.
 *
 * Variables: $pageTitle, $pageDescription, $currentPage
 */
$pageTitle = $pageTitle ?? 'Home';
$pageDescription = $pageDescription ?? 'Browse copier machine PDF catalogs by brand. Download product brochures instantly.';
$currentPage = $currentPage ?? '';
$basePath = CATALOG_BASE_PATH;
?>
<!DOCTYPE html>
<html lang="en-US">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($pageTitle) ?> - <?= htmlspecialchars(CATALOG_SITE_NAME) ?></title>
    <meta name="description" content="<?= htmlspecialchars($pageDescription) ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Poppins:wght@500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet" crossorigin="anonymous">
    <link rel="stylesheet" href="<?= $basePath ?>assets/css/style.css">
    <link rel="stylesheet" href="<?= $basePath ?>assets/css/responsive.css">
</head>
<body>
<a class="visually-hidden-focusable skip-link" href="#content">Skip to content</a>

<header class="site-header sticky-top" id="masthead">
    <nav class="navbar navbar-expand-lg navbar-light bg-white">
        <div class="container">
            <a class="navbar-brand site-logo" href="<?= $basePath ?>">
                <img src="<?= catalog_url('assets/images/logo.png') ?>" alt="<?= htmlspecialchars(CATALOG_SITE_NAME) ?>" width="181" height="60" loading="eager">
            </a>

            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar"
                    aria-controls="mainNavbar" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="mainNavbar">
                <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link<?= $currentPage === 'home' ? ' active' : '' ?>" href="<?= $basePath ?>">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link<?= $currentPage === 'download-copier-brochures' ? ' active' : '' ?>" href="<?= $basePath ?>download-copier-brochures/">Download Brochures</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link<?= $currentPage === 'other-brochures' ? ' active' : '' ?>" href="<?= $basePath ?>other-brochures/">Other Brochures</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link<?= $currentPage === 'about' ? ' active' : '' ?>" href="<?= $basePath ?>about/">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link<?= $currentPage === 'blog' ? ' active' : '' ?>" href="https://brochure.copiercatalog.com/blog/">Blog</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link<?= $currentPage === 'contact' ? ' active' : '' ?>" href="<?= $basePath ?>contact/">Contact</a>
                    </li>
                </ul>

                <form class="header-search-form d-flex" role="search" method="get" action="<?= $basePath ?>">
                    <div class="input-group">
                        <input type="search" class="form-control" name="q" placeholder="Search brochure"
                               value="<?= htmlspecialchars($_GET['q'] ?? '') ?>" aria-label="Search brochure">
                        <button class="btn btn-primary" type="submit" aria-label="Search">
                            <i class="fa-solid fa-magnifying-glass"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </nav>
</header>

<main id="content">
