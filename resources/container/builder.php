<?php

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Resource\FileResource;

$containerBuilder = new ContainerBuilder();
$containerBuilder->addResource(new FileResource(__FILE__));

$loader = new YamlFileLoader(
    $containerBuilder,
    new FileLocator(__DIR__ . '/config')
);

/** @noinspection PhpUnhandledExceptionInspection */
$loader->load('services.yml');

return $containerBuilder;
