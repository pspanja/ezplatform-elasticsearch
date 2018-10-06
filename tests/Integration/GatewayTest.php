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
    public function testBulkIndex(): void
    {
        $gateway = $this->getGatewayUnderTest();
        $endpoint = Endpoint::fromDsn('http://localhost:9200/test');
        $payload = <<<'EOD'
{"index":{"_index":"test","_type":"temporary","_id":"1"}}
{"field":"value"}

EOD;

        $response = $gateway->bulkIndex($endpoint, $payload);

        $this->assertEquals(200, $response->status);
    }

    /**
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
     * @depends testBulkIndex
     * @depends testFlush
     *
     * @throws \Exception
     */
    public function testFind(): void
    {
        $gateway = $this->getGatewayUnderTest();
        $endpoint = Endpoint::fromDsn('http://localhost:9200/test');
        $gateway->flush($endpoint);

        $query = [
            'query' => [
                'term' => [
                    'field' => 'value',
                ],
            ],
        ];
        $response = $gateway->find($endpoint, 'temporary', $query);

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
