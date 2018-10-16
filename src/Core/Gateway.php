<?php

declare(strict_types=1);

namespace Cabbage\Core;

use Cabbage\Core\Http\Client;
use Cabbage\Core\Http\Message;
use Cabbage\SPI\Endpoint;
use RuntimeException;

/**
 * Communicates with Elasticsearch server using an HTTP client.
 */
final class Gateway
{
    /**
     * @var \Cabbage\Core\Http\Client
     */
    private $client;

    /**
     * @param \Cabbage\Core\Http\Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @param \Cabbage\SPI\Endpoint $endpoint
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
     * @param \Cabbage\SPI\Endpoint $endpoint
     * @param array|array[] $query
     *
     * @return string
     */
    public function find(Endpoint $endpoint, array $query): string
    {
        $url = "{$endpoint->getUrl()}/temporary/_search";
        $message = Message::fromJson((string)json_encode($query));

        $response = $this->client->get($url, $message);

        if ($response->status !== 200) {
            throw new RuntimeException("Invalid response status {$response->status}");
        }

        return $response->body;
    }

    /**
     * @param \Cabbage\SPI\Endpoint $endpoint
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
