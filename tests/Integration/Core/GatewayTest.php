<?php

declare(strict_types=1);

namespace Cabbage\Tests\Integration\Core;

use Cabbage\SPI\Endpoint;

class GatewayTest extends BaseTest
{
    /**
     * @var \Cabbage\SPI\Endpoint
     */
    private static $endpoint;

    /**
     * @var \Cabbage\Core\Gateway
     */
    private static $gateway;

    /**
     * @throws \Exception
     */
    public static function setUpBeforeClass(): void
    {
        self::$endpoint = Endpoint::fromDsn('http://localhost:9200/gateway_test');
        self::$gateway = self::getContainer()->get('cabbage.gateway');
        $configurator = self::getContainer()->get('cabbage.configurator');

        if ($configurator->hasIndex(self::$endpoint)) {
            $configurator->deleteIndex(self::$endpoint);
        }

        $mapping = \file_get_contents(__DIR__ . '/../../../config/elasticsearch/mapping.json');

        $configurator->createIndex(self::$endpoint);
        $configurator->setMapping(self::$endpoint, $mapping);
    }

    /**
     * @testdox Index can be flushed
     *
     * @throws \Exception
     */
    public function testFlush(): void
    {
        self::$gateway->flush(self::$endpoint);

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
        $endpoint = self::$endpoint;
        $payload = <<<EOD
{"index":{"_index":"{$endpoint->index}","_type":"_doc","_id":"a_1"}}
{"type_identifier":"type_a","field_keyword":"value"}
{"index":{"_index":"{$endpoint->index}","_type":"_doc","_id":"b_1"}}
{"type_identifier":"type_b","field_keyword":"value"}

EOD;

        self::$gateway->index(self::$endpoint, $payload);
        self::$gateway->flush(self::$endpoint);

        $this->assertTrue(true);
    }

    /**
     * @testdox Documents can be found by field value
     * @depends testBulkIndex
     *
     * @throws \Exception
     */
    public function testFindByFieldValue(): void
    {
        $query = [
            'query' => [
                'term' => [
                    'field_keyword' => 'value',
                ],
            ],
        ];

        $data = self::$gateway->find(self::$endpoint, $query);
        $data = json_decode($data);

        $this->assertEquals(2, $data->hits->total);
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

        $data = self::$gateway->find(self::$endpoint, $query);
        $data = json_decode($data);

        $this->assertEquals(1, $data->hits->total);
    }
}
