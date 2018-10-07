<?php

declare(strict_types=1);

namespace Cabbage\Tests\Integration;

use Cabbage\Document;
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

        if (self::$gateway->hasIndex(self::$endpoint)) {
            self::$gateway->deleteIndex(self::$endpoint);
        }
    }

    /**
     * @throws \Exception
     */
    public function testCreateIndex(): void
    {
        $response = self::$gateway->createIndex(self::$endpoint);

        $this->assertEquals(200, $response->status);
    }

    /**
     * @depends testCreateIndex
     *
     * @throws \Exception
     */
    public function testFlush(): void
    {
        $response = self::$gateway->flush(self::$endpoint);

        $this->assertEquals(200, $response->status);
    }

    /**
     * @depends testFlush
     *
     * @throws \Exception
     */
    public function testBulkIndex(): void
    {
        $endpoint = self::$endpoint;
        $payload = <<<EOD
{"index":{"_index":"{$endpoint->index}","_type":"temporary","_id":"content_1"}}
{"type":"content","field":"content_value"}
{"index":{"_index":"{$endpoint->index}","_type":"temporary","_id":"location_1"}}
{"type":"location","field":"location_value"}

EOD;

        $response = self::$gateway->bulkIndex(self::$endpoint, $payload);
        self::$gateway->flush(self::$endpoint);

        $this->assertEquals(200, $response->status);
    }

    /**
     * @depends testBulkIndex
     *
     * @throws \Exception
     */
    public function testFindContent(): void
    {
        $query = [
            'query' => [
                'bool' => [
                    'must' => [
                        [
                            'term' => [
                                'type' => Document::TypeContent,
                            ],
                        ],
                        [
                            'term' => [
                                'field' => 'content_value',
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $response = self::$gateway->find(self::$endpoint, $query);

        $this->assertEquals(200, $response->status);

        $body = json_decode($response->body);

        $this->assertEquals(1, $body->hits->total);
    }

    /**
     * @depends testBulkIndex
     *
     * @throws \Exception
     */
    public function testFindLocation(): void
    {
        $query = [
            'query' => [
                'bool' => [
                    'must' => [
                        [
                            'term' => [
                                'type' => Document::TypeLocation,
                            ],
                        ],
                        [
                            'term' => [
                                'field' => 'location_value',
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $response = self::$gateway->find(self::$endpoint, $query);

        $this->assertEquals(200, $response->status);

        $body = json_decode($response->body);

        $this->assertEquals(1, $body->hits->total);
    }
}
