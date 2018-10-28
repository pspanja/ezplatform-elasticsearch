<?php

declare(strict_types=1);

namespace Cabbage\Tests\Integration;

use PHPUnit\Framework\TestCase;
use RuntimeException;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Throwable;

abstract class BaseTest extends TestCase
{
    /**
     * @var \Symfony\Component\DependencyInjection\Container
     */
    private static $serviceContainer;

    /**
     * @return \Symfony\Component\DependencyInjection\Container
     */
    protected static function getContainer(): Container
    {
        if (self::$serviceContainer === null) {
            self::$serviceContainer = self::buildContainer();
        }

        return self::$serviceContainer;
    }

    /**
     * @return \Symfony\Component\DependencyInjection\ContainerBuilder
     */
    private static function buildContainer(): ContainerBuilder
    {
        $containerBuilder = include __DIR__ . '/container_builder.php';

        $loader = new YamlFileLoader(
            $containerBuilder,
            new FileLocator(__DIR__ . '/resources/container')
        );

        try {
            $loader->load('services.yml');
        } catch (Throwable $e) {
            throw new RuntimeException($e->getMessage());
        }

        return $containerBuilder;
    }
}
