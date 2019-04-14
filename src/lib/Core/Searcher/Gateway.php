<?php

declare(strict_types=1);

namespace Cabbage\Core\Searcher;

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
     * @param array|array[] $query
     *
     * @return string
     */
    public function find(Index $index, array $query): string
    {
        $url = "{$index->getUrl()}/_search";
        $message = Message::fromHash($query);

        $response = $this->client->get($url, $message);

        if ($response->status !== 200) {
            throw new RuntimeException(
                "Invalid response status {$response->status}"
            );
        }

        return $response->body;
    }
}
