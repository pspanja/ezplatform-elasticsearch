<?php

declare(strict_types=1);

namespace Cabbage\Tests;

use Cabbage\Endpoint;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class EndpointTest extends TestCase
{
    /**
     * @return array|array[]
     */
    public function providerForBuildFromDsn(): array
    {
        return [
            $dsn = 'http://localhost:9200/location' => [
                $dsn,
                new Endpoint(
                    'http',
                    'localhost',
                    9200,
                    'location'
                ),
            ],
            $dsn = 'localhost:9200/location' => [
                $dsn,
                new Endpoint(
                    'http',
                    'localhost',
                    9200,
                    'location'
                ),
            ],
            $dsn = 'http://localhost/location' => [
                $dsn,
                new Endpoint(
                    'http',
                    'localhost',
                    9200,
                    'location'
                ),
            ],
        ];
    }

    /**
     * @testdox Endpoint can be built from a correct DSN
     * @dataProvider providerForBuildFromDsn
     *
     * @param string $dsn
     * @param \Cabbage\Endpoint $expectedEndpoint
     */
    public function testBuildFromDsn(string $dsn, Endpoint $expectedEndpoint): void
    {
        $this->assertEquals($expectedEndpoint, Endpoint::fromDsn($dsn));
    }

    /**
     * @return array|array[]
     */
    public function providerForFailToBuildFromDsn(): array
    {
        return [
            $dsn = 'localhost/location' => [
                $dsn,
                'Index name must not contain a slash',
            ],
            $dsn = 'http://localhost:abcd/location' => [
                $dsn,
                'Failed to parse the given DSN',
            ],
            $dsn = 'http://localhost/path/location' => [
                $dsn,
                'Index name must not contain a slash',
            ],
        ];
    }

    /**
     * @testdox Endpoint can't be built from a incorrect DSN
     * @dataProvider providerForFailToBuildFromDsn
     *
     * @param string $dsn
     * @param string $message
     */
    public function testFailToBuildFromDsn(string $dsn, string $message): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage($message);

        Endpoint::fromDsn($dsn);
    }
}
