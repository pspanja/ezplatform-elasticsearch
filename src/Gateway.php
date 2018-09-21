<?php

declare(strict_types=1);

namespace Cabbage;

use Cabbage\Http\Client;
use Cabbage\Http\Request;
use Cabbage\Http\Response;

/**
 * The gateway communicates with Elasticsearch server using HTTP client.
 */
final class Gateway
{
    /**
     * @var \Cabbage\Http\Client
     */
    private $client;

    /**
     * @param \Cabbage\Http\Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function ping(string $uri): Response
    {
        $request = new Request($uri);

        return $this->client->send($request);
    }
}
