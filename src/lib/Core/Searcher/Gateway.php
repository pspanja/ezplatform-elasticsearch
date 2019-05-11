<?php

declare(strict_types=1);

namespace Cabbage\Core\Searcher;

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
     * @param string[] $indices
     * @param array|array[] $query
     *
     * @return string
     */
    public function find(Node $node, array $indices, array $query): string
    {
        $url = $node->getUrl(). '/' . implode(',', $indices) . '/_search';

        $response = $this->client->get(Message::fromHash($query), $url);

        if ($response->status !== 200) {
            throw new RuntimeException(
                'Invalid response status ' . $response->status
            );
        }

        return $response->body;
    }
}
