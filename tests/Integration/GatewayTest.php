<?php

declare(strict_types=1);

namespace Cabbage\Tests\Integration;

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

        $gateway->createIndex($endpoint);
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
{"type":"content","field":"value"}
{"index":{"_index":"test","_type":"temporary","_id":"location_1"}}
{"type":"location","field":"value"}

EOD;

        $response = $gateway->bulkIndex($endpoint, $payload);

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
                'term' => [
                    'field' => 'value',
                ],
            ],
        ];

        $response = $gateway->findContent($endpoint, $query);

        $this->assertEquals(200, $response->status);

        $body = json_decode($response->body);

        $this->assertGreaterThanOrEqual(1, $body->hits->total);
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
                'term' => [
                    'field' => 'value',
                ],
            ],
        ];

        $response = $gateway->findLocations($endpoint, $query);

        $this->assertEquals(200, $response->status);

        $body = json_decode($response->body);

        $this->assertGreaterThanOrEqual(1, $body->hits->total);
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
