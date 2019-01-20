<?php

declare(strict_types=1);

namespace Cabbage\Tests\Unit;

use Cabbage\SPI\Node;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class NodeTest extends TestCase
{
    /**
     * @return array|array[]
     */
    public function providerForBuildFromDsn(): array
    {
        return [
            $dsn = 'http://localhost:9200' => [
                $dsn,
                new Node(
                    'http',
                    'localhost',
                    9200
                ),
            ],
            $dsn = 'localhost:9200' => [
                $dsn,
                new Node(
                    'http',
                    'localhost',
                    9200
                ),
            ],
            $dsn = 'http://localhost' => [
                $dsn,
                new Node(
                    'http',
                    'localhost',
                    9200
                ),
            ],
        ];
    }

    /**
     * @testdox Node can be built from a correct DSN
     * @dataProvider providerForBuildFromDsn
     *
     * @param string $dsn
     * @param \Cabbage\SPI\Node $expectedNode
     */
    public function testBuildFromDsn(string $dsn, Node $expectedNode): void
    {
        $this->assertEquals($expectedNode, Node::fromDsn($dsn));
    }

    /**
     * @return array|array[]
     */
    public function providerForFailToBuildFromDsn(): array
    {
        return [
            $dsn = 'http://localhost:abcd' => [
                $dsn,
                'Failed to parse the given DSN',
            ],
            $dsn = 'http://user:pass@localhost' => [
                $dsn,
                "DSN does not support the 'user' and 'pass' components"
            ],
            $dsn = 'http://user@localhost' => [
                $dsn,
                "DSN does not support the 'user' and 'pass' components"
            ],
            $dsn = 'http://localhost/path' => [
                $dsn,
                "DSN does not support the 'path' component"
            ],
            $dsn = 'http://localhost?name=value' => [
                $dsn,
                "DSN does not support the 'query' component"
            ],
            $dsn = 'http://localhost#fragment' => [
                $dsn,
                "DSN does not support the 'fragment' component"
            ],
        ];
    }

    /**
     * @testdox Node can't be built from a incorrect DSN
     * @dataProvider providerForFailToBuildFromDsn
     *
     * @param string $dsn
     * @param string $message
     */
    public function testFailToBuildFromDsn(string $dsn, string $message): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage($message);

        Node::fromDsn($dsn);
    }
}
