<?php
/**
 * Generate and serve cached PDF first-page thumbnails.
 */
declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/functions.php';

$relativePath = $_GET['path'] ?? '';
if ($relativePath === '' || !preg_match('#^[a-z0-9][a-z0-9_-]*/[a-zA-Z0-9_. -]+\.pdf$#i', $relativePath)) {
    http_response_code(400);
    exit('Invalid path');
}

$pdfPath = CATALOG_ROOT . '/' . $relativePath;
$realPdf = realpath($pdfPath);
if ($realPdf === false || !is_file($realPdf) || !str_starts_with($realPdf, CATALOG_ROOT)) {
    http_response_code(404);
    exit('PDF not found');
}

$categoryDir = basename(dirname($relativePath));
if (in_array($categoryDir, catalog_excluded_dirs(), true)) {
    http_response_code(403);
    exit('Forbidden');
}

$thumbDir = CATALOG_ROOT . '/cache/thumbs';
if (!is_dir($thumbDir)) {
    mkdir($thumbDir, 0755, true);
}

$mtime = filemtime($realPdf) ?: 0;
$cacheKey = md5($relativePath . '|' . $mtime);
$thumbFile = $thumbDir . '/' . $cacheKey . '.jpg';

if (!is_file($thumbFile)) {
    $tmpBase = $thumbDir . '/' . $cacheKey . '_tmp';
    $cmd = sprintf(
        '/usr/bin/pdftoppm -jpeg -f 1 -l 1 -scale-to 400 %s %s 2>/dev/null',
        escapeshellarg($realPdf),
        escapeshellarg($tmpBase)
    );
    exec($cmd, $output, $exitCode);

    $generated = glob($tmpBase . '-*.jpg')[0] ?? '';
    if ($exitCode !== 0 || $generated === '' || !is_file($generated)) {
        http_response_code(500);
        exit('Thumbnail generation failed');
    }

    rename($generated, $thumbFile);
    foreach (glob($tmpBase . '*') ?: [] as $leftover) {
        @unlink($leftover);
    }
}

header('Content-Type: image/jpeg');
header('Cache-Control: public, max-age=604800');
header('Expires: ' . gmdate('D, d M Y H:i:s', time() + 604800) . ' GMT');
header('Content-Length: ' . filesize($thumbFile));
readfile($thumbFile);
