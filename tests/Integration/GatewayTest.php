<?php

declare(strict_types=1);

namespace Cabbage\Tests\Integration;

use Cabbage\Document;
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
        $document = new Document('test', ['field' => 'test']);

        $response = $gateway->index('http://localhost:9200', $document);

        $this->assertEquals(201, $response->status);
    }

    public function getGatewayUnderTest(): Gateway
    {
        return new Gateway(new Client());
    }
}
