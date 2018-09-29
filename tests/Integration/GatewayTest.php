<?php

declare(strict_types=1);

namespace Cabbage\Tests\Integration;

use Cabbage\Document;
use Cabbage\DocumentSerializer;
use Cabbage\Field;
use Cabbage\Gateway;
use Cabbage\Http\Client;
use PHPUnit\Framework\TestCase;

class GatewayTest extends TestCase
{
    public function testCreateIndex(): void
    {
        $gateway = $this->getGatewayUnderTest();

        $response = $gateway->createIndex('http://localhost:9200', 'test');

        $this->assertEquals(200, $response->status);
    }

    public function testIndex(): void
    {
        $gateway = $this->getGatewayUnderTest();
        $fields = [
            new Field('test_string', 'value', 'string'),
            new Field('test_bool', true, 'bool'),
        ];
        $document = new Document('test', $fields);

        $response = $gateway->index('http://localhost:9200', 'test', $document);

        $this->assertEquals(201, $response->status);
    }

    public function testFlush(): void
    {
        $gateway = $this->getGatewayUnderTest();

        $response = $gateway->flush('http://localhost:9200');

        $this->assertEquals(200, $response->status);
    }

    public function testFind(): void
    {
        $gateway = $this->getGatewayUnderTest();
        $uri = 'http://localhost:9200';
        $index = 'test';
        $fields = [
            new Field('field', 'value', 'string'),
        ];
        $document = new Document('test', $fields);

        $gateway->index($uri, $index, $document);
        $gateway->flush($uri);
        $response = $gateway->find($uri, $index, $document->type, 'field', 'value');

        $this->assertEquals(200, $response->status);

        $body = json_decode($response->body);

        $this->assertGreaterThanOrEqual(1, $body->hits->total);
    }

    public function getGatewayUnderTest(): Gateway
    {
        return new Gateway(
            new Client(),
            new DocumentSerializer()
        );
    }
}
