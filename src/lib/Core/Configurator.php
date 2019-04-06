<?php

declare(strict_types=1);

namespace Cabbage\Core;

use Cabbage\Core\Http\Client;
use Cabbage\Core\Http\Message;
use Cabbage\Core\Http\Response;
use Cabbage\SPI\Index;
use RuntimeException;

/**
 * Provides low level API for configuring Elasticsearch server by communicating
 * with it using an HTTP client.
 */
final class Configurator
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
     *
     * @return \Cabbage\Core\Http\Response
     */
    public function createIndex(Index $index): Response
    {
        $body = [
            'settings' => [
                'number_of_shards' => 1,
                'index.write.wait_for_active_shards' => 1,
            ],
        ];

        $message = new Message(
            json_encode($body, JSON_THROW_ON_ERROR),
            [
                'Content-Type' => 'application/json',
            ]
        );

        return $this->client->put($index->getUrl(), $message);
    }

    /**
     * @param \Cabbage\SPI\Index $index
     *
     * @return bool
     */
    public function hasIndex(Index $index): bool
    {
        $message = new Message();

        $response = $this->client->head($index->getUrl(), $message);

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
     * @param \Cabbage\SPI\Index $index
     *
     * @return \Cabbage\Core\Http\Response
     */
    public function deleteIndex(Index $index): Response
    {
        return $this->client->delete($index->getUrl());
    }

    /**
     * @param \Cabbage\SPI\Index $index
     * @param string $mapping
     *
     * @return \Cabbage\Core\Http\Response
     */
    public function setMapping(Index $index, string $mapping): Response
    {
        return $this->client->put(
            $index->getUrl() . '/_mapping',
            Message::fromString($mapping)
        );
    }
}
