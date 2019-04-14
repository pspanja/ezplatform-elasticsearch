<?php

declare(strict_types=1);

namespace Cabbage\Core\Indexer;

use Cabbage\Core\Http\Client;
use Cabbage\Core\Http\Message;
use Cabbage\SPI\Index;
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
     * @param \Cabbage\SPI\Index $index
     * @param string $payload
     */
    public function index(Index $index, string $payload): void
    {
        $url = "{$index->getUrl()}/_bulk";

        $message = new Message(
            $payload,
            [
                'Content-Type' => 'application/x-ndjson',
            ]
        );

        $response = $this->client->post($url, $message);

        if ($response->status !== 200) {
            throw new RuntimeException(
                "Invalid response status {$response->status}"
            );
        }
    }

    /**
     * @param \Cabbage\SPI\Index $index
     */
    public function flush(Index $index): void
    {
        $url = "{$index->getUrl()}/_flush";

        $response = $this->client->post($url);

        if ($response->status !== 200) {
            throw new RuntimeException(
                "Invalid response status {$response->status}"
            );
        }
    }

    /**
     * @param \Cabbage\SPI\Index $index
     */
    public function refresh(Index $index): void
    {
        $url = "{$index->getUrl()}/_refresh";

        $response = $this->client->post($url);

        if ($response->status !== 200) {
            throw new RuntimeException(
                "Invalid response status {$response->status}"
            );
        }
    }

    /**
     * @param \Cabbage\SPI\Index $index
     */
    public function purge(Index $index): void
    {
        $url = "{$index->getUrl()}/_delete_by_query";
        $query = [
            'query' => [
                'match_all' => (object)null,
            ],
        ];

        $response = $this->client->post(
            $url,
            Message::fromHash($query)
        );

        if ($response->status !== 200 && $response->status !== 404) {
            throw new RuntimeException(
                "Invalid response status {$response->status}"
            );
        }
    }
}
