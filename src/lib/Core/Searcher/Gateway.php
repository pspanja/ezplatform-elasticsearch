<?php

declare(strict_types=1);

namespace Cabbage\Core\Searcher;

use Cabbage\Core\Http\Client;
use Cabbage\Core\Http\Message;
use Cabbage\Core\Searcher\Query\Target;
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
     * @param \Cabbage\Core\Searcher\Query\Target $target
     * @param array|array[] $query
     *
     * @return string
     */
    public function find(Target $target, array $query): string
    {
        $response = $this->client->get(
            $target->getUrl(),
            Message::fromHash($query)
        );

        if ($response->status !== 200) {
            throw new RuntimeException(
                "Invalid response status {$response->status}"
            );
        }

        return $response->body;
    }
}
