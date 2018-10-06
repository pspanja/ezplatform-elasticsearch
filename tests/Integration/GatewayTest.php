<?php

declare(strict_types=1);

namespace Cabbage\Tests\Integration;

use Cabbage\Document;
use Cabbage\Endpoint;
use Cabbage\Gateway;

class GatewayTest extends BaseTest
{
    /**
     * @throws \Exception
     */
    public function testCreateIndex(): void
    {
        $gateway = $this->getGatewayUnderTest();
        $endpoint = Endpoint::fromDsn('http://localhost:9200/test');

        $response = $gateway->createIndex($endpoint);

        $this->assertEquals(200, $response->status);
    }

    /**
     * @depends testCreateIndex
     *
     * @throws \Exception
     */
    public function testFlush(): void
    {
        $gateway = $this->getGatewayUnderTest();
        $endpoint = Endpoint::fromDsn('http://localhost:9200/test');

        $response = $gateway->flush($endpoint);

        $this->assertEquals(200, $response->status);
    }

    /**
     * @depends testFlush
     *
     * @throws \Exception
     */
    public function testBulkIndex(): void
    {
        $gateway = $this->getGatewayUnderTest();
        $endpoint = Endpoint::fromDsn('http://localhost:9200/test');
        $payload = <<<'EOD'
{"index":{"_index":"test","_type":"temporary","_id":"content_1"}}
{"type":"content","field":"content_value"}
{"index":{"_index":"test","_type":"temporary","_id":"location_1"}}
{"type":"location","field":"location_value"}

EOD;

        $response = $gateway->bulkIndex($endpoint, $payload);
        $gateway->flush($endpoint);

        $this->assertEquals(200, $response->status);
    }

    /**
     * @depends testBulkIndex
     *
     * @throws \Exception
     */
    public function testFindContent(): void
    {
        $gateway = $this->getGatewayUnderTest();
        $endpoint = Endpoint::fromDsn('http://localhost:9200/test');
        $query = [
            'query' => [
                'bool' => [
                    'must' => [
                        [
                            'term' => [
                                'type' => Document::TypeContent,
                            ]
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

        $response = $gateway->find($endpoint, $query);

        $this->assertEquals(200, $response->status);

        $body = json_decode($response->body);

        $this->assertEquals(1, $body->hits->total);
    }

    /**
     * @depends testBulkIndex
     *
     * @throws \Exception
     */
    public function testFindLocations(): void
    {
        $gateway = $this->getGatewayUnderTest();
        $endpoint = Endpoint::fromDsn('http://localhost:9200/test');
        $query = [
            'query' => [
                'bool' => [
                    'must' => [
                        [
                            'term' => [
                                'type' => Document::TypeLocation,
                            ]
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

        $response = $gateway->find($endpoint, $query);

        $this->assertEquals(200, $response->status);

        $body = json_decode($response->body);

        $this->assertEquals(1, $body->hits->total);
    }

    /**
     * @throws \Exception
     *
     * @return \Cabbage\Gateway
     */
    public function getGatewayUnderTest(): Gateway
    {
        return $this->getContainer()->get('cabbage.gateway');
    }
}
