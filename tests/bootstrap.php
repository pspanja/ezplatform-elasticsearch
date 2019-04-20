<?php

declare(strict_types=1);

$file = __DIR__ . '/../vendor/ezsystems/ezpublish-kernel/config.php';

if (!file_exists($file) && !symlink($file . '-DEVELOPMENT', $file)) {
    throw new RuntimeException('Could not symlink config.php-DEVELOPMENT to config.php');
}
