<?php

declare(strict_types=1);

namespace Cabbage\Tests\Integration;

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

    public function getGatewayUnderTest(): Gateway
    {
        return new Gateway(new Client());
    }
}
