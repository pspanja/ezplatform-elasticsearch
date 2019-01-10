<?php

namespace Cabbage\Tests\Integration\API;

use eZ\Publish\API\Repository\Repository;
use eZ\Publish\API\Repository\Tests\SetupFactory;
use PHPUnit\Framework\TestCase;
use RuntimeException;

/**
 * Base class for API integration tests.
 */
abstract class BaseTest extends TestCase
{
    /**
     * @var \eZ\Publish\API\Repository\Tests\SetupFactory
     */
    private $setupFactory;

    /**
     * @var \eZ\Publish\API\Repository\Repository
     */
    private $repository;

    protected function tearDown(): void
    {
        $this->repository = null;

        parent::tearDown();
    }

    /**
     * @param bool $reset
     *
     * @return \eZ\Publish\API\Repository\Repository
     */
    protected function getRepository($reset = true): Repository
    {
        if ($this->repository === null) {
            $this->repository = $this->getSetupFactory()->getRepository($reset);
        }

        return $this->repository;
    }

    protected function getSetupFactory(): SetupFactory
    {
        if ($this->setupFactory === null) {
            $this->setupFactory = $this->buildSetupFactory();
        }

        return $this->setupFactory;
    }

    private function buildSetupFactory(): Repository
    {
        $setupClass = getenv('setupFactory');

        if (false === $setupClass) {
            throw new RuntimeException(
                'Missing mandatory environment variable "setupFactory"'
            );
        }

        if (false === class_exists($setupClass)) {
            throw new RuntimeException(
                "SetupFactory '{$setupClass}' is not an existing class"
            );
        }

        return new $setupClass();
    }
}
