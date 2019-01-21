<?php

declare(strict_types=1);

namespace Cabbage\Tests\Integration\Core;

use Cabbage\SPI\Index;
use Cabbage\SPI\Node;

class GatewayTest extends BaseTest
{
    /**
     * @var \Cabbage\SPI\Index
     */
    private static $index;

    /**
     * @var \Cabbage\Core\Gateway
     */
    private static $gateway;

    /**
     * @throws \Exception
     */
    public static function setUpBeforeClass(): void
    {
        $node = Node::fromDsn('http://localhost:9200');
        self::$index = new Index($node, 'gateway_test');
        self::$gateway = self::getContainer()->get('cabbage.gateway');
        $configurator = self::getContainer()->get('cabbage.configurator');

        if ($configurator->hasIndex(self::$index)) {
            $configurator->deleteIndex(self::$index);
        }

        $mapping = \file_get_contents(__DIR__ . '/../../../config/elasticsearch/mapping.json');

        $configurator->createIndex(self::$index);
        $configurator->setMapping(self::$index, $mapping);
    }

    /**
     * @testdox Index can be flushed
     *
     * @throws \Exception
     */
    public function testFlush(): void
    {
        self::$gateway->flush(self::$index);

        $this->assertTrue(true);
    }

    /**
     * @testdox Data can be indexed
     * @depends testFlush
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

        self::$gateway->index(self::$index, $payload);
        self::$gateway->flush(self::$index);

        $this->assertTrue(true);
    }

    /**
     * @testdox All documents can be found
     * @depends testBulkIndex
     *
     * @throws \Exception
     */
    public function testFindAll(): void
    {
        $query = [
            'query' => [
                'match_all' => (object)null,
            ],
        ];

        $data = self::$gateway->find(self::$index, $query);
        $data = json_decode($data);

        $this->assertEquals(2, $data->hits->total->value);
    }

    /**
     * @testdox Documents can be found by type
     * @depends testBulkIndex
     *
     * @throws \Exception
     */
    public function testFindByType(): void
    {
        $query = [
            'query' => [
                'term' => [
                    'type_identifier' => 'type_b',
                ],
            ],
        ];

        $data = self::$gateway->find(self::$index, $query);
        $data = json_decode($data);

        $this->assertEquals(1, $data->hits->total->value);
    }

    /**
     * @testdox Index can be purged
     * @depends testFindAll
     */
    public function testPurge(): void
    {
        self::$gateway->purge(self::$index);
        self::$gateway->flush(self::$index);

        $query = [
            'query' => [
                'match_all' => (object)null,
            ],
        ];

        $data = self::$gateway->find(self::$index, $query);
        $data = json_decode($data);

        $this->assertEquals(0, $data->hits->total->value);
    }
}
