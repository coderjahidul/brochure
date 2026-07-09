<?php
/**
 * Generate and serve cached PDF first-page thumbnails.
 * Uses Imagick on shared hosting; falls back to pdftoppm when available.
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

if (!is_file($thumbFile) && !catalog_generate_pdf_thumbnail($realPdf, $thumbFile, $thumbDir, $cacheKey)) {
    http_response_code(500);
    exit('Thumbnail generation failed');
}

header('Content-Type: image/jpeg');
header('Cache-Control: public, max-age=604800');
header('Expires: ' . gmdate('D, d M Y H:i:s', time() + 604800) . ' GMT');
header('Content-Length: ' . (string) filesize($thumbFile));
readfile($thumbFile);

/**
 * Generate a JPEG thumbnail from the first page of a PDF.
 */
function catalog_generate_pdf_thumbnail(
    string $pdfPath,
    string $thumbFile,
    string $thumbDir,
    string $cacheKey
): bool {
    if (catalog_thumbnail_via_imagick($pdfPath, $thumbFile)) {
        return true;
    }

    return catalog_thumbnail_via_pdftoppm($pdfPath, $thumbFile, $thumbDir, $cacheKey);
}

/**
 * Render PDF page 1 via Imagick (works on Hostinger shared hosting).
 */
function catalog_thumbnail_via_imagick(string $pdfPath, string $thumbFile): bool
{
    if (!extension_loaded('imagick')) {
        return false;
    }

    try {
        $image = new Imagick();
        $image->setResolution(150, 150);
        $image->readImage($pdfPath . '[0]');
        $image->setImageFormat('jpeg');
        $image->setImageCompressionQuality(85);
        $image->thumbnailImage(400, 0);
        $image->writeImage($thumbFile);
        $image->clear();
        $image->destroy();
    } catch (Throwable) {
        return false;
    }

    return is_file($thumbFile);
}

/**
 * Render PDF page 1 via pdftoppm (local dev fallback).
 */
function catalog_thumbnail_via_pdftoppm(
    string $pdfPath,
    string $thumbFile,
    string $thumbDir,
    string $cacheKey
): bool {
    if (!function_exists('exec')) {
        return false;
    }

    $pdftoppm = catalog_find_pdftoppm();
    if ($pdftoppm === null) {
        return false;
    }

    $tmpBase = $thumbDir . '/' . $cacheKey . '_tmp';
    $cmd = sprintf(
        '%s -jpeg -f 1 -l 1 -scale-to 400 %s %s 2>/dev/null',
        escapeshellarg($pdftoppm),
        escapeshellarg($pdfPath),
        escapeshellarg($tmpBase)
    );
    exec($cmd, $output, $exitCode);

    $generated = glob($tmpBase . '-*.jpg')[0] ?? '';
    if ($exitCode !== 0 || $generated === '' || !is_file($generated)) {
        foreach (glob($tmpBase . '*') ?: [] as $leftover) {
            @unlink($leftover);
        }

        return false;
    }

    rename($generated, $thumbFile);
    foreach (glob($tmpBase . '*') ?: [] as $leftover) {
        @unlink($leftover);
    }

    return is_file($thumbFile);
}

/**
 * Locate the pdftoppm binary.
 */
function catalog_find_pdftoppm(): ?string
{
    static $resolved = null;
    if ($resolved !== null) {
        return $resolved === '' ? null : $resolved;
    }

    foreach (['/usr/bin/pdftoppm', '/usr/local/bin/pdftoppm'] as $candidate) {
        if (is_executable($candidate)) {
            $resolved = $candidate;

            return $resolved;
        }
    }

    if (function_exists('exec')) {
        exec('which pdftoppm 2>/dev/null', $output, $exitCode);
        $which = trim($output[0] ?? '');
        if ($exitCode === 0 && $which !== '' && is_executable($which)) {
            $resolved = $which;

            return $resolved;
        }
    }

    $resolved = '';

    return null;
}
