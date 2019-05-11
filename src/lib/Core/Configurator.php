<?php

declare(strict_types=1);

namespace Cabbage\Core;

use Cabbage\Core\HttpClient\Client;
use Cabbage\Core\HttpClient\Message;
use Cabbage\Core\HttpClient\Response;
use Cabbage\SPI\Node;
use RuntimeException;

/**
 * Provides low level API for configuring Elasticsearch server by communicating
 * with it using an HTTP client.
 */
final class Configurator
{
    /**
     * @var \Cabbage\Core\HttpClient\Client
     */
    private $client;

    /**
     * @param \Cabbage\Core\HttpClient\Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @param \Cabbage\SPI\Node $node
     * @param string $index
     *
     * @return \Cabbage\Core\HttpClient\Response
     */
    public function createIndex(Node $node, string $index): Response
    {
        $body = [
            'settings' => [
                'number_of_shards' => 1,
                'index.write.wait_for_active_shards' => 1,
            ],
        ];

        $url = $node->getUrl() . '/' . $index;
        $message = new Message(
            json_encode($body, JSON_THROW_ON_ERROR),
            [
                'Content-Type' => 'application/json',
            ]
        );

        return $this->client->put($message, $url);
    }

    /**
     * @param \Cabbage\SPI\Node $node
     * @param string $index
     *
     * @return bool
     */
    public function hasIndex(Node $node, string $index): bool
    {
        $url = $node->getUrl() . '/' . $index;
        $message = new Message();

        $response = $this->client->head($message, $url);

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
     * @param \Cabbage\SPI\Node $node
     * @param string $index
     *
     * @return \Cabbage\Core\HttpClient\Response
     */
    public function deleteIndex(Node $node, string $index): Response
    {
        $url = $node->getUrl() . '/' . $index;

        return $this->client->delete(new Message(), $url);
    }

    /**
     * @param \Cabbage\SPI\Node $node
     * @param string $index
     * @param string $mapping
     *
     * @return \Cabbage\Core\HttpClient\Response
     */
    public function setMapping(Node $node, string $index, string $mapping): Response
    {
        $url = $node->getUrl() . '/' . $index . '/_mapping';

        return $this->client->put(Message::fromString($mapping), $url);
    }
}
