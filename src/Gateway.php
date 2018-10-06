<?php

declare(strict_types=1);

namespace Cabbage;

use Cabbage\Http\Client;
use Cabbage\Http\Request;
use Cabbage\Http\Response;
use RuntimeException;

/**
 * Communicates with Elasticsearch server using an HTTP client.
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
     * @param string $payload
     *
     * @return \Cabbage\Http\Response
     */
    public function bulkIndex(Endpoint $endpoint, string $payload): Response
    {
        $url = "{$endpoint->getUrl()}/_bulk";

        $request = new Request(
            $payload,
            [
                'Content-Type' => 'application/x-ndjson',
            ]
        );

        $response = $this->client->post($request, $url);

        if ($response->status !== 200) {
            throw new RuntimeException("Invalid response status {$response->status}");
        }

        return $response;
    }

    /**
     * @param \Cabbage\Endpoint $endpoint
     * @param array|array[] $query
     *
     * @return \Cabbage\Http\Response
     */
    public function find(Endpoint $endpoint, array $query): Response
    {
        $url = "{$endpoint->getUrl()}/temporary/_search";
        $request = new Request(
            (string)json_encode($query),
            [
                'Content-Type' => 'application/json',
            ]
        );

        $response = $this->client->get($request, $url);

        if ($response->status !== 200) {
            throw new RuntimeException("Invalid response status {$response->status}");
        }

        return $response;
    }

    /**
     * @param \Cabbage\Endpoint $endpoint
     *
     * @return \Cabbage\Http\Response
     */
    public function flush(Endpoint $endpoint): Response
    {
        $url = "{$endpoint->getUrl()}/_flush";
        $request = new Request();

        return $this->client->post($request, $url);
    }
}
