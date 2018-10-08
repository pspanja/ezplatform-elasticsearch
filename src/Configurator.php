<?php

declare(strict_types=1);

namespace Cabbage;

use Cabbage\Http\Client;
use Cabbage\Http\Request;
use Cabbage\Http\Response;
use RuntimeException;

/**
 * Provides low level API for configuring Elasticsearch server by communicating
 * with it using an HTTP client.
 */
final class Configurator
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

    /**
     * @param \Cabbage\Endpoint $endpoint
     *
     * @return \Cabbage\Http\Response
     */
    public function createIndex(Endpoint $endpoint): Response
    {
        $body = [
            'settings' => [
                'number_of_shards' => 1,
                'index.write.wait_for_active_shards' => 1,
            ],
        ];

        $request = new Request(
            (string)json_encode($body),
            [
                'Content-Type' => 'application/json',
            ]
        );

        return $this->client->put($request, $endpoint->getUrl());
    }

    /**
     * @param \Cabbage\Endpoint $endpoint
     *
     * @return bool
     */
    public function hasIndex(Endpoint $endpoint): bool
    {
        $request = new Request();

        $response = $this->client->head($request, $endpoint->getUrl());

        if ($response->status === 200) {
            return true;
        }

        if ($response->status === 404) {
            return false;
        }

        throw new RuntimeException(
            "Invalid response status {$response->status}"
        );
    }

    /**
     * @param \Cabbage\Endpoint $endpoint
     *
     * @return \Cabbage\Http\Response
     */
    public function deleteIndex(Endpoint $endpoint): Response
    {
        $request = new Request();

        return $this->client->delete($request, $endpoint->getUrl());
    }
}
