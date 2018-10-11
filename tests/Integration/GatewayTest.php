<?php

declare(strict_types=1);

namespace Cabbage\Tests\Integration;

use Cabbage\Endpoint;

class GatewayTest extends BaseTest
{
    /**
     * @var \Cabbage\Endpoint
     */
    private static $endpoint;

    /**
     * @var \Cabbage\Gateway
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

        $configurator->createIndex(self::$endpoint);
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
{"index":{"_index":"{$endpoint->index}","_type":"temporary","_id":"a_1"}}
{"type":"type_a","field":"value"}
{"index":{"_index":"{$endpoint->index}","_type":"temporary","_id":"b_1"}}
{"type":"type_b","field":"value"}

EOD;

        $response = self::$gateway->bulkIndex(self::$endpoint, $payload);
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
                    'field' => 'value',
                ],
            ],
        ];

        $response = self::$gateway->find(self::$endpoint, $query);

        $this->assertEquals(200, $response->status);

        $body = json_decode($response->body);

        $this->assertEquals(2, $body->hits->total);
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
                    'type' => 'type_b',
                ],
            ],
        ];

        $response = self::$gateway->find(self::$endpoint, $query);

        $this->assertEquals(200, $response->status);

        $body = json_decode($response->body);

        $this->assertEquals(1, $body->hits->total);
    }
}
