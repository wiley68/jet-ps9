<?php

declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit;
}

$temporaryFile = null;
$exitCode = 0;

try {
    if (!class_exists(ZipArchive::class)) {
        throw new RuntimeException('The PHP zip extension is required.');
    }

    $moduleDir = dirname(__DIR__);
    $source = file_get_contents($moduleDir . '/creditjet.php');
    if ($source === false || !preg_match('/\$this->version\s*=\s*[\'"]([0-9][0-9A-Za-z.+-]*)[\'"]\s*;/', $source, $matches)) {
        throw new RuntimeException('Cannot read the module version from creditjet.php.');
    }

    $distDir = $moduleDir . '/dist';
    if (!is_dir($distDir) && !mkdir($distDir, 0775, true)) {
        throw new RuntimeException('Cannot create dist directory.');
    }

    $archivePath = $distDir . '/CC_PrestaShop_9_JET_v.' . $matches[1] . '.zip';
    $temporaryFile = tempnam($distDir, '.build-');
    if ($temporaryFile === false) {
        throw new RuntimeException('Cannot create temporary archive.');
    }

    $zip = new ZipArchive();
    if ($zip->open($temporaryFile, ZipArchive::OVERWRITE) !== true) {
        throw new RuntimeException('Cannot open temporary archive.');
    }

    // Explicit package contents keep local settings and development tools out of the ZIP.
    $paths = [
        'creditjet.php', 'index.php', 'logo.png', 'config.xml', 'config_bg.xml',
        'composer.json', 'composer.lock', 'CHANGELOG.md', 'sbom.json',
        'config', 'controllers', 'css', 'documents', 'js', 'mails', 'sql',
        'src', 'translations', 'views',
    ];

    foreach ($paths as $path) {
        $fullPath = $moduleDir . '/' . $path;
        $files = is_dir($fullPath)
            ? new RecursiveIteratorIterator(new RecursiveCallbackFilterIterator(
                new RecursiveDirectoryIterator($fullPath, FilesystemIterator::SKIP_DOTS),
                static fn (SplFileInfo $file): bool => !str_starts_with($file->getFilename(), '.') && !$file->isLink()
            ))
            : [new SplFileInfo($fullPath)];

        foreach ($files as $file) {
            if (!$file->isFile() || $file->isLink()) {
                throw new RuntimeException('Missing or unsupported package file: ' . $file->getPathname());
            }
            $relativePath = substr($file->getPathname(), strlen($moduleDir) + 1);
            if (!$zip->addFile($file->getPathname(), 'creditjet/' . $relativePath)) {
                throw new RuntimeException('Cannot add file: ' . $relativePath);
            }
        }
    }

    if (!$zip->close() || !rename($temporaryFile, $archivePath)) {
        throw new RuntimeException('Cannot save the installation archive.');
    }
    $temporaryFile = null;

    foreach (glob($distDir . '/CC_PrestaShop_9_JET_v.*.zip') ?: [] as $oldArchive) {
        if ($oldArchive !== $archivePath && !unlink($oldArchive)) {
            throw new RuntimeException('Cannot remove old archive: ' . basename($oldArchive));
        }
    }

    fwrite(STDOUT, 'Created: ' . $archivePath . PHP_EOL);
} catch (Throwable $error) {
    fwrite(STDERR, 'Build failed: ' . $error->getMessage() . PHP_EOL);
    $exitCode = 1;
} finally {
    unset($zip);
    if (is_string($temporaryFile) && is_file($temporaryFile)) {
        unlink($temporaryFile);
    }
}

exit($exitCode);
