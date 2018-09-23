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
    public function testPing(): void
    {
        $gateway = $this->getGatewayUnderTest();

        $response = $gateway->ping('http://localhost:9200');

        $this->assertEquals(200, $response->status);

        $body = json_decode($response->body);

        $this->assertEquals('You Know, for Search', $body->tagline);
    }

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

        $response = $gateway->index('http://localhost:9200', $document);

        $this->assertEquals(201, $response->status);
    }

    public function getGatewayUnderTest(): Gateway
    {
        return new Gateway(
            new Client(),
            new DocumentSerializer()
        );
    }
}
