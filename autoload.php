<?php
spl_autoload_register(function ($namespace) {
    $relativePath = str_replace(
        ['App\\', '\\'],
        ['', DIRECTORY_SEPARATOR],
        $namespace
    ) . '.php';

    $file = __DIR__ . DIRECTORY_SEPARATOR . $relativePath;

    if (file_exists($file)) {
        require_once $file;
        return;
    }

    $file = __DIR__ . DIRECTORY_SEPARATOR . strtolower($relativePath);

    if (file_exists($file)) {
        require_once $file;
        return;
    }

    $parts = explode(DIRECTORY_SEPARATOR, $relativePath);
    $currentPath = __DIR__;

    foreach ($parts as $part) {
        if ($part === end($parts)) {
            $files = glob($currentPath . DIRECTORY_SEPARATOR . '*');
            foreach ($files as $potentialFile) {
                if (strtolower(basename($potentialFile)) === strtolower($part)) {
                    require_once $potentialFile;
                    return;
                }
            }
        } else {
            $dirs = glob($currentPath . DIRECTORY_SEPARATOR . '*', GLOB_ONLYDIR);
            foreach ($dirs as $dir) {
                if (strtolower(basename($dir)) === strtolower($part)) {
                    $currentPath = $dir;
                    break;
                }
            }
        }
    }
});

$helpersPath = __DIR__ . '/helpers';
if (is_dir($helpersPath)) {
    $helperFiles = glob($helpersPath . '/*.php');
    foreach ($helperFiles as $file) {
        require_once $file;
    }
}
