<?php

declare(strict_types=1);

namespace Cabbage\Tests\Integration\Core\Indexer;

use Cabbage\SPI\Index;
use Cabbage\SPI\Node;
use Cabbage\Tests\Integration\Core\BaseTest;
use function file_get_contents;

class GatewayTest extends BaseTest
{
    /**
     * @var \Cabbage\SPI\Index
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
        $node = Node::fromDsn('http://localhost:9200');
        self::$index = new Index($node, 'indexer_gateway_test');
        self::$gateway = self::getContainer()->get('cabbage.indexer.gateway');
        $configurator = self::getContainer()->get('cabbage.configurator');

        if ($configurator->hasIndex(self::$index)) {
            $configurator->deleteIndex(self::$index);
        }

        $mapping = file_get_contents(__DIR__ . '/../../../../config/elasticsearch/mapping.json');

        $configurator->createIndex(self::$index);
        $configurator->setMapping(self::$index, $mapping);
    }

    /**
     * @testdox Index can be refreshed
     *
     * @throws \Exception
     */
    public function testRefresh(): void
    {
        self::$gateway->refresh(self::$index->node);

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
{"index":{"_index":"{$index->name}","_id":"a_1"}}
{"type_identifier":"type_a","field_keyword":"value"}
{"index":{"_index":"{$index->name}","_id":"b_1"}}
{"type_identifier":"type_b","field_keyword":"value"}

EOD;

        self::$gateway->index(self::$index->node, $payload);
        self::$gateway->refresh(self::$index->node);

        $this->addToAssertionCount(1);
    }

    /**
     * @testdox Index can be purged
     * @depends testRefresh
     */
    public function testPurge(): void
    {
        self::$gateway->purge(self::$index->node);
        self::$gateway->refresh(self::$index->node);

        $this->addToAssertionCount(1);
    }
}
