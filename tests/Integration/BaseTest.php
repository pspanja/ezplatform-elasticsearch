<?php

declare(strict_types=1);

namespace Cabbage\Tests\Integration;

use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\Container;

abstract class BaseTest extends TestCase
{
    /**
     * @var \Symfony\Component\DependencyInjection\Container
     */
    private static $serviceContainer;

    /**
     * @return \Symfony\Component\DependencyInjection\Container
     */
    public function getContainer(): Container
    {
        if (self::$serviceContainer === null) {
            self::$serviceContainer = include __DIR__ . '/../../resources/container/builder.php';
        }

        return self::$serviceContainer;
    }
}
