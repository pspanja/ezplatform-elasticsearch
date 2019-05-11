<?php

declare(strict_types=1);

namespace Cabbage\Tests\Integration\Core\Indexer;

use Cabbage\SPI\Node;
use Cabbage\Tests\Integration\Core\BaseTest;
use function file_get_contents;

class GatewayTest extends BaseTest
{
    /**
     * @var \Cabbage\SPI\Node
     */
    private static $node;

    /**
     * @var string
     */
    private static $index;

    /**
     * @var \Cabbage\Core\Indexer\Gateway
     */
    private static $gateway;

    /**
     * @throws \Exception
     */
    public static function setUpBeforeClass(): void
    {
        self::$node = Node::fromDsn('http://localhost:9200');
        self::$index = 'indexer_gateway_test';
        self::$gateway = self::getContainer()->get('cabbage.indexer.gateway');
        $configurator = self::getContainer()->get('cabbage.configurator');

        if ($configurator->hasIndex(self::$node, self::$index)) {
            $configurator->deleteIndex(self::$node, self::$index);
        }

        $mapping = file_get_contents(__DIR__ . '/../../../../config/elasticsearch/mapping.json');

        $configurator->createIndex(self::$node, self::$index);
        $configurator->setMapping(self::$node, self::$index, $mapping);
    }

    /**
     * @testdox Index can be refreshed
     *
     * @throws \Exception
     */
    public function testRefresh(): void
    {
        self::$gateway->refresh(self::$node);

        $this->addToAssertionCount(1);
    }

    /**
     * @testdox Data can be indexed
     * @depends testRefresh
     *
     * @throws \Exception
     */
    public function testBulkIndex(): void
    {
        $index = self::$index;
        $payload = <<<EOD
{"index":{"_index":"{$index}","_id":"a_1"}}
{"type_identifier":"type_a","field_keyword":"value"}
{"index":{"_index":"{$index}","_id":"b_1"}}
{"type_identifier":"type_b","field_keyword":"value"}

EOD;

        self::$gateway->index(self::$node, $payload);
        self::$gateway->refresh(self::$node);

        $this->addToAssertionCount(1);
    }

    /**
     * @testdox Index can be purged
     * @depends testRefresh
     */
    public function testPurge(): void
    {
        self::$gateway->purge(self::$node);
        self::$gateway->refresh(self::$node);

        $this->addToAssertionCount(1);
    }
}
