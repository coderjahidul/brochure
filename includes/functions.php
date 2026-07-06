<?php
/**
 * PDF Catalog � core functions
 * Scans brand folders under the site root for PDF files automatically.
 */

declare(strict_types=1);

/** Site configuration */
define('CATALOG_SITE_NAME', 'Copier Catalog');
define('CATALOG_ROOT', dirname(__DIR__));
define('CATALOG_EXCLUDED_DIRS', ['api', 'assets', 'cache', 'includes', 'pdf-catalogs']);
define('CATALOG_CACHE_FILE', CATALOG_ROOT . '/cache/catalog-cache.json');
define('CATALOG_CACHE_TTL', 300); // 5 minutes
define('CATALOG_HOMEPAGE_LIMIT', 3);
define('CATALOG_PER_PAGE', 12);

/**
 * Auto-detect base URL path from the running script.
 */
function catalog_base_path(): string
{
    static $base = null;
    if ($base !== null) {
        return $base;
    }
    $script = $_SERVER['SCRIPT_NAME'] ?? '/index.php';
    $dir = str_replace('\\', '/', dirname($script));
    $base = ($dir === '/' || $dir === '.') ? '/' : rtrim($dir, '/') . '/';
    return $base;
}

define('CATALOG_BASE_PATH', catalog_base_path());

/**
 * Convert a folder name to a URL slug.
 */
function catalog_slugify(string $name): string
{
    $slug = strtolower(trim($name));
    $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);
    return trim($slug, '-') ?: 'category';
}

/**
 * Convert slug back to display title (best effort).
 */
function catalog_title_from_slug(string $slug): string
{
    return ucwords(str_replace('-', ' ', $slug));
}

/**
 * Format bytes to human-readable file size.
 */
function catalog_format_size(int $bytes): string
{
    if ($bytes >= 1048576) {
        return round($bytes / 1048576, 1) . ' MB';
    }
    if ($bytes >= 1024) {
        return round($bytes / 1024, 1) . ' KB';
    }
    return $bytes . ' B';
}

/**
 * Build public URL for a path relative to site root.
 */
function catalog_url(string $path = ''): string
{
    return rtrim(CATALOG_BASE_PATH, '/') . '/' . ltrim($path, '/');
}

/**
 * Resolve a PDF's relative file path (works with fresh scan data and legacy cache).
 */
function catalog_pdf_relative_path(array $pdf): string
{
    if (!empty($pdf['path'])) {
        $path = $pdf['path'];
        if (str_starts_with($path, 'pdf-catalogs/')) {
            $path = strtolower(substr($path, strlen('pdf-catalogs/')));
        }

        return $path;
    }

    $folder = $pdf['category_dir'] ?? $pdf['category'];

    return $folder . '/' . $pdf['filename'];
}

/**
 * Directories excluded from automatic category scanning.
 *
 * @return array<int, string>
 */
function catalog_excluded_dirs(): array
{
    return CATALOG_EXCLUDED_DIRS;
}

/**
 * Brand/category folders to scan for PDFs.
 *
 * @return array<int, string>
 */
function catalog_category_dirs(): array
{
    $excluded = array_flip(catalog_excluded_dirs());
    $dirs = glob(CATALOG_ROOT . '/*', GLOB_ONLYDIR) ?: [];
    $dirs = array_values(array_filter(
        $dirs,
        static fn(string $path) => !isset($excluded[basename($path)])
    ));
    sort($dirs);

    return $dirs;
}

/**
 * Latest modification time across all category folders (for cache invalidation).
 */
function catalog_scan_mtime(): int
{
    $mtime = 0;
    foreach (catalog_category_dirs() as $dirPath) {
        $mtime = max($mtime, filemtime($dirPath) ?: 0);
        foreach (glob($dirPath . '/*.pdf') ?: [] as $pdfPath) {
            $mtime = max($mtime, filemtime($pdfPath) ?: 0);
        }
    }

    return $mtime;
}

/**
 * Add public URLs from stored relative paths (never cache absolute URLs).
 *
 * @param array<string, mixed> $pdf
 * @return array<string, mixed>
 */
function catalog_hydrate_pdf(array $pdf): array
{
    $path = catalog_pdf_relative_path($pdf);
    $pdf['path'] = $path;
    $pdf['url'] = catalog_url($path);
    $pdf['view_url'] = $pdf['url'];
    $pdf['download_url'] = $pdf['url'];

    $thumbPath = $pdf['thumb_path'] ?? '';
    $pdf['thumb_url'] = $thumbPath !== ''
        ? catalog_url($thumbPath)
        : catalog_pdf_thumb_url($path);

    return $pdf;
}

/**
 * @param array<string, mixed> $category
 * @return array<string, mixed>
 */
function catalog_hydrate_category(array $category): array
{
    $category['url'] = catalog_url('category/' . $category['slug'] . '/');
    $category['pdfs'] = array_map('catalog_hydrate_pdf', $category['pdfs'] ?? []);

    return $category;
}

/**
 * @param array<string, mixed> $data
 * @return array{categories: array<int, array<string, mixed>>, all_pdfs: array<int, array<string, mixed>>}
 */
function catalog_hydrate_scan(array $data): array
{
    $categories = array_map('catalog_hydrate_category', $data['categories'] ?? []);
    $allPdfs = array_map('catalog_hydrate_pdf', $data['all_pdfs'] ?? []);

    return ['categories' => $categories, 'all_pdfs' => $allPdfs];
}

/**
 * Get catalog cache if still valid.
 *
 * @return array<string, mixed>|null
 */
function catalog_get_cache(): ?array
{
    if (!is_file(CATALOG_CACHE_FILE)) {
        return null;
    }

    $raw = file_get_contents(CATALOG_CACHE_FILE);
    if ($raw === false) {
        return null;
    }

    $data = json_decode($raw, true);
    if (!is_array($data) || !isset($data['built_at'], $data['catalog_mtime'])) {
        return null;
    }

    $scanMtime = catalog_scan_mtime();
    $expired = (time() - (int) $data['built_at']) > CATALOG_CACHE_TTL;

    if ($expired || (int) $data['catalog_mtime'] !== $scanMtime) {
        return null;
    }

    return $data;
}

/**
 * Persist catalog scan results to cache.
 *
 * @param array<string, mixed> $data
 */
function catalog_set_cache(array $data): void
{
    $dir = dirname(CATALOG_CACHE_FILE);
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }

    $data['built_at'] = time();
    $data['catalog_mtime'] = catalog_scan_mtime();

    file_put_contents(
        CATALOG_CACHE_FILE,
        json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES),
        LOCK_EX
    );
}

/**
 * Scan all category folders and PDF files.
 *
 * @return array{categories: array<int, array<string, mixed>>, all_pdfs: array<int, array<string, mixed>>}
 */
function catalog_scan(): array
{
    $cached = catalog_get_cache();
    if ($cached !== null) {
        return catalog_hydrate_scan($cached);
    }

    $categories = [];
    $allPdfs = [];

    foreach (catalog_category_dirs() as $dirPath) {
        $folderName = basename($dirPath);
        $slug = catalog_slugify($folderName);
        $displayName = catalog_title_from_slug($slug);
        $descriptionFile = $dirPath . '/description.txt';
        $description = is_file($descriptionFile)
            ? trim((string) file_get_contents($descriptionFile))
            : 'Browse our collection of ' . $folderName . ' product catalogs and brochures.';

        $pdfs = catalog_scan_pdfs_in_dir($dirPath, $folderName, $slug, $displayName);
        usort($pdfs, static fn($a, $b) => $b['mtime'] <=> $a['mtime']);

        if ($pdfs === []) {
            continue;
        }

        $category = [
            'name' => $displayName,
            'slug' => $slug,
            'description' => $description,
            'pdf_count' => count($pdfs),
            'pdfs' => $pdfs,
        ];

        $categories[] = $category;
        $allPdfs = array_merge($allPdfs, $pdfs);
    }

    usort($allPdfs, static fn($a, $b) => $b['mtime'] <=> $a['mtime']);

    catalog_set_cache([
        'categories' => $categories,
        'all_pdfs' => $allPdfs,
    ]);

    return catalog_hydrate_scan([
        'categories' => $categories,
        'all_pdfs' => $allPdfs,
    ]);
}

/**
 * Scan PDF files inside a single category directory.
 *
 * @return array<int, array<string, mixed>>
 */
function catalog_scan_pdfs_in_dir(
    string $dirPath,
    string $categoryDir,
    string $categorySlug,
    string $categoryName
): array {
    $pdfs = [];
    $files = glob($dirPath . '/*.pdf') ?: [];
    sort($files);

    foreach ($files as $filePath) {
        $filename = basename($filePath);
        $title = catalog_pdf_title($filename);
        $size = filesize($filePath) ?: 0;
        $mtime = filemtime($filePath) ?: 0;
        $relativePath = $categoryDir . '/' . $filename;

        $thumbPath = catalog_find_thumbnail($filePath);
        $thumbRelativePath = $thumbPath
            ? str_replace(CATALOG_ROOT . '/', '', $thumbPath)
            : '';

        $pdfs[] = [
            'title' => $title,
            'filename' => $filename,
            'category' => $categoryName,
            'category_dir' => $categoryDir,
            'category_slug' => $categorySlug,
            'size' => $size,
            'size_label' => catalog_format_size((int) $size),
            'mtime' => $mtime,
            'date_label' => date('M j, Y', $mtime),
            'path' => $relativePath,
            'thumb_path' => $thumbRelativePath,
        ];
    }

    return $pdfs;
}

/**
 * Derive display title from PDF filename.
 */
function catalog_pdf_title(string $filename): string
{
    $name = pathinfo($filename, PATHINFO_FILENAME);
    $name = str_replace(['_', '-'], ' ', $name);
    return ucwords($name);
}

/**
 * URL for auto-generated PDF first-page thumbnail.
 */
function catalog_pdf_thumb_url(string $relativePath): string
{
    return catalog_url('api/thumbnail.php?path=' . rawurlencode($relativePath));
}

/**
 * Look for optional thumbnail image alongside PDF.
 */
function catalog_find_thumbnail(string $pdfPath): ?string
{
    $base = pathinfo($pdfPath, PATHINFO_DIRNAME) . '/' . pathinfo($pdfPath, PATHINFO_FILENAME);

    foreach (['.jpg', '.jpeg', '.png', '.webp', '-thumb.jpg', '-thumb.png'] as $ext) {
        $candidate = $base . $ext;
        if (is_file($candidate)) {
            return $candidate;
        }
    }

    return null;
}

/**
 * Get all categories.
 *
 * @return array<int, array<string, mixed>>
 */
function catalog_get_categories(): array
{
    return catalog_scan()['categories'];
}

/**
 * Find category by slug.
 *
 * @return array<string, mixed>|null
 */
function catalog_get_category(string $slug): ?array
{
    foreach (catalog_get_categories() as $category) {
        if ($category['slug'] === $slug) {
            return $category;
        }
    }
    return null;
}

/**
 * Paginate an array.
 *
 * @param array<int, mixed> $items
 * @return array{items: array<int, mixed>, page: int, total_pages: int, total: int, per_page: int}
 */
function catalog_paginate(array $items, int $page, int $perPage = CATALOG_PER_PAGE): array
{
    $total = count($items);
    $totalPages = max(1, (int) ceil($total / $perPage));
    $page = max(1, min($page, $totalPages));
    $offset = ($page - 1) * $perPage;

    return [
        'items' => array_slice($items, $offset, $perPage),
        'page' => $page,
        'total_pages' => $totalPages,
        'total' => $total,
        'per_page' => $perPage,
    ];
}

/**
 * Search PDFs and categories by keyword.
 *
 * @return array{pdfs: array<int, array<string, mixed>>, categories: array<int, array<string, mixed>>}
 */
function catalog_search(string $query): array
{
    $query = trim(strtolower($query));
    if ($query === '') {
        return ['pdfs' => [], 'categories' => []];
    }

    $data = catalog_scan();
    $matchedPdfs = [];
    $matchedCategories = [];

    foreach ($data['categories'] as $category) {
        $catMatch = str_contains(strtolower($category['name']), $query)
            || str_contains($category['slug'], $query);

        if ($catMatch) {
            $matchedCategories[] = $category;
        }

        foreach ($category['pdfs'] as $pdf) {
            if (
                str_contains(strtolower($pdf['title']), $query)
                || str_contains(strtolower($pdf['filename']), $query)
                || str_contains(strtolower($category['name']), $query)
            ) {
                $matchedPdfs[] = $pdf;
            }
        }
    }

    return ['pdfs' => $matchedPdfs, 'categories' => $matchedCategories];
}

/**
 * Filter PDFs within a category by keyword.
 *
 * @param array<int, array<string, mixed>> $pdfs
 * @return array<int, array<string, mixed>>
 */
function catalog_filter_pdfs(array $pdfs, string $query): array
{
    $query = trim(strtolower($query));
    if ($query === '') {
        return $pdfs;
    }

    return array_values(array_filter(
        $pdfs,
        static fn($pdf) => str_contains(strtolower($pdf['title']), $query)
            || str_contains(strtolower($pdf['filename']), $query)
    ));
}

/**
 * Render pagination links.
 */
function catalog_render_pagination(int $currentPage, int $totalPages, string $baseUrl, array $extraParams = []): string
{
    if ($totalPages <= 1) {
        return '';
    }

    $html = '<nav class="catalog-pagination" aria-label="Pagination"><ul class="pagination justify-content-center">';

    $buildUrl = static function (int $page) use ($baseUrl, $extraParams): string {
        $params = array_merge($extraParams, ['page' => $page]);
        $qs = http_build_query($params);
        return $baseUrl . ($qs ? '?' . $qs : '');
    };

    $prevDisabled = $currentPage <= 1 ? ' disabled' : '';
    $html .= '<li class="page-item' . $prevDisabled . '">';
    $html .= '<a class="page-link" href="' . ($currentPage > 1 ? htmlspecialchars($buildUrl($currentPage - 1)) : '#') . '" aria-label="Previous">';
    $html .= '<i class="fa-solid fa-chevron-left"></i></a></li>';

    $range = 2;
    $start = max(1, $currentPage - $range);
    $end = min($totalPages, $currentPage + $range);

    if ($start > 1) {
        $html .= '<li class="page-item"><a class="page-link" href="' . htmlspecialchars($buildUrl(1)) . '">1</a></li>';
        if ($start > 2) {
            $html .= '<li class="page-item disabled"><span class="page-link">�</span></li>';
        }
    }

    for ($i = $start; $i <= $end; $i++) {
        $active = $i === $currentPage ? ' active' : '';
        $html .= '<li class="page-item' . $active . '">';
        $html .= '<a class="page-link" href="' . htmlspecialchars($buildUrl($i)) . '">' . $i . '</a></li>';
    }

    if ($end < $totalPages) {
        if ($end < $totalPages - 1) {
            $html .= '<li class="page-item disabled"><span class="page-link">�</span></li>';
        }
        $html .= '<li class="page-item"><a class="page-link" href="' . htmlspecialchars($buildUrl($totalPages)) . '">' . $totalPages . '</a></li>';
    }

    $nextDisabled = $currentPage >= $totalPages ? ' disabled' : '';
    $html .= '<li class="page-item' . $nextDisabled . '">';
    $html .= '<a class="page-link" href="' . ($currentPage < $totalPages ? htmlspecialchars($buildUrl($currentPage + 1)) : '#') . '" aria-label="Next">';
    $html .= '<i class="fa-solid fa-chevron-right"></i></a></li>';

    $html .= '</ul></nav>';
    return $html;
}

/**
 * Render a single PDF card.
 */
function catalog_render_pdf_card(array $pdf): string
{
    $thumbUrl = $pdf['thumb_url'] ?? '';
    $thumb = $thumbUrl !== ''
        ? '<img src="' . htmlspecialchars($thumbUrl) . '" alt="" class="pdf-card__thumb-img" loading="lazy" width="400" height="520">'
        : '<div class="pdf-card__thumb-placeholder" aria-hidden="true"><i class="fa-solid fa-file-pdf"></i><span>PDF</span></div>';

    return '<article class="pdf-card h-100">'
        . '<a href="' . htmlspecialchars($pdf['view_url']) . '" class="pdf-card__thumb-link" target="_blank" rel="noopener" aria-label="View ' . htmlspecialchars($pdf['title']) . '">'
        . '<div class="pdf-card__thumb">' . $thumb . '</div>'
        . '</a>'
        . '<div class="pdf-card__body">'
        . '<h3 class="pdf-card__title">' . htmlspecialchars($pdf['title']) . '</h3>'
        . '<p class="pdf-card__meta"><i class="fa-regular fa-hard-drive"></i> ' . htmlspecialchars($pdf['size_label']) . '</p>'
        . '<div class="pdf-card__actions">'
        . '<a href="' . htmlspecialchars($pdf['view_url']) . '" class="btn btn-primary btn-sm" target="_blank" rel="noopener">'
        . '<i class="fa-regular fa-eye"></i> View PDF</a>'
        . '<a href="' . htmlspecialchars($pdf['download_url']) . '" class="btn btn-outline-primary btn-sm" download>'
        . '<i class="fa-solid fa-download"></i> Download</a>'
        . '</div></div></article>';
}

/**
 * Render a grid of PDF cards.
 *
 * @param array<int, array<string, mixed>> $pdfs
 */
function catalog_render_pdf_grid(array $pdfs): string
{
    if (empty($pdfs)) {
        return '<div class="catalog-empty"><i class="fa-regular fa-folder-open"></i><p>No PDF catalogs found.</p></div>';
    }

    $html = '<div class="row g-4 pdf-grid">';
    foreach ($pdfs as $pdf) {
        $html .= '<div class="col-12 col-sm-6 col-lg-4">' . catalog_render_pdf_card($pdf) . '</div>';
    }
    $html .= '</div>';
    return $html;
}
