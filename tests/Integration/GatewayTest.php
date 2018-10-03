<?php

declare(strict_types=1);

namespace Cabbage\Tests\Integration;

use Cabbage\Document;
use Cabbage\Endpoint;
use Cabbage\Field;
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
     * @throws \Exception
     */
    public function testIndex(): void
    {
        $gateway = $this->getGatewayUnderTest();
        $endpoint = Endpoint::fromDsn('http://localhost:9200/test');
        $fields = [
            new Field('test_string', 'value', 'string'),
            new Field('test_bool', true, 'bool'),
        ];
        $document = new Document('test', $fields);

        $response = $gateway->index($endpoint, $document);

        $this->assertEquals(201, $response->status);
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
     * @throws \Exception
     */
    public function testFind(): void
    {
        $gateway = $this->getGatewayUnderTest();
        $endpoint = Endpoint::fromDsn('http://localhost:9200/test');
        $fields = [
            new Field('field', 'value', 'string'),
        ];
        $document = new Document('test', $fields);

        $gateway->index($endpoint, $document);
        $gateway->flush($endpoint);

        $query = [
            'query' => [
                'term' => [
                    'field' => 'value',
                ],
            ],
        ];
        $response = $gateway->find($endpoint, $document->type, $query);

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
