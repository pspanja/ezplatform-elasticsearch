<?php

declare(strict_types=1);

namespace Cabbage\Core\Indexer;

use Cabbage\Core\Http\Client;
use Cabbage\Core\Http\Message;
use Cabbage\SPI\Node;
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
     * @param \Cabbage\SPI\Node $node
     * @param string $payload
     */
    public function index(Node $node, string $payload): void
    {
        $url = "{$node->getUrl()}/_bulk";

        $message = new Message(
            $payload,
            [
                'Content-Type' => 'application/x-ndjson',
            ]
        );

        $response = $this->client->post($message, $url);

        if ($response->status !== 200) {
            throw new RuntimeException(
                "Invalid response status {$response->status}"
            );
        }
    }

    /**
     * @param \Cabbage\SPI\Node $node
     */
    public function flush(Node $node): void
    {
        $url = "{$node->getUrl()}/_all/_flush";

        $response = $this->client->post(new Message(), $url);

        if ($response->status !== 200) {
            throw new RuntimeException(
                "Invalid response status {$response->status}"
            );
        }
    }

    /**
     * @param \Cabbage\SPI\Node $node
     */
    public function refresh(Node $node): void
    {
        $url = "{$node->getUrl()}/_all/_refresh";

        $response = $this->client->post(new Message(), $url);

        if ($response->status !== 200) {
            throw new RuntimeException(
                "Invalid response status {$response->status}"
            );
        }
    }

    /**
     * @param \Cabbage\SPI\Node $node
     */
    public function purge(Node $node): void
    {
        $url = "{$node->getUrl()}/_all/_delete_by_query";
        $query = [
            'query' => [
                'match_all' => (object)null,
            ],
        ];

        $response = $this->client->post(
            Message::fromHash($query),
            $url
        );

        if ($response->status !== 200 && $response->status !== 404) {
            throw new RuntimeException(
                "Invalid response status {$response->status}"
            );
        }
    }
}
