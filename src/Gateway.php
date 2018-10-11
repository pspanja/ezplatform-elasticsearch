<?php

declare(strict_types=1);

namespace Cabbage;

use Cabbage\Http\Client;
use Cabbage\Http\Message;
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
     * @param string $payload
     */
    public function bulkIndex(Endpoint $endpoint, string $payload): void
    {
        $url = "{$endpoint->getUrl()}/_bulk";

        $message = new Message(
            $payload,
            [
                'Content-Type' => 'application/x-ndjson',
            ]
        );

        $response = $this->client->post($url, $message);

        if ($response->status !== 200) {
            throw new RuntimeException("Invalid response status {$response->status}");
        }
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
        $message = Message::fromJson((string)json_encode($query));

        $response = $this->client->get($url, $message);

        if ($response->status !== 200) {
            throw new RuntimeException("Invalid response status {$response->status}");
        }

        return $response;
    }

    /**
     * @param \Cabbage\Endpoint $endpoint
     */
    public function flush(Endpoint $endpoint): void
    {
        $url = "{$endpoint->getUrl()}/_flush";

        $response = $this->client->post($url);

        if ($response->status !== 200) {
            throw new RuntimeException("Invalid response status {$response->status}");
        }
    }
}
