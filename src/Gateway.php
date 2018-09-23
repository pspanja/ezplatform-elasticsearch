<?php

declare(strict_types=1);

namespace Cabbage;

use Cabbage\Http\Client;
use Cabbage\Http\Request;
use Cabbage\Http\Response;
use RuntimeException;

/**
 * The gateway communicates with Elasticsearch server using HTTP client.
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

    public function ping(string $uri): Response
    {
        $request = new Request();

        return $this->client->get($request, $uri);
    }

    public function createIndex(string $uri, string $name): Response
    {
        $uri = "{$uri}/{$name}";
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

        return $this->client->put($request, $uri);
    }

    public function index(string $uri, Document $document): Response
    {
        $uri = "{$uri}/test/{$document->type}";

        $request = new Request(
            $this->serialize($document),
            [
                'Content-Type' => 'application/json',
            ]
        );

        return $this->client->post($request, $uri);
    }

    private function serialize(Document $document): string
    {
        $content = [];

        foreach ($document->fields as $field) {
            $content[$field->name] = $field->value;
        }

        $content = json_encode($content);

        if ($content === false) {
            throw new RuntimeException('Could not JSON encode given document');
        }

        return $content;
    }
}
