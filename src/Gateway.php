<?php

declare(strict_types=1);

namespace Cabbage;

use Cabbage\Http\Client;
use Cabbage\Http\Request;
use Cabbage\Http\Response;
use RuntimeException;

/**
 * Communicates with Elasticsearch server using HTTP client.
 */
final class Gateway
{
    /**
     * @var \Cabbage\Http\Client
     */
    private $client;

    /**
     * @var \Cabbage\DocumentSerializer
     */
    private $documentSerializer;

    /**
     * @param \Cabbage\Http\Client $client
     * @param \Cabbage\DocumentSerializer $documentSerializer
     */
    public function __construct(Client $client, DocumentSerializer $documentSerializer)
    {
        $this->client = $client;
        $this->documentSerializer = $documentSerializer;
    }

    /**
     * @param \Cabbage\Endpoint $endpoint
     *
     * @return \Cabbage\Http\Response
     */
    public function createIndex(Endpoint $endpoint): Response
    {
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

        return $this->client->put($request, $endpoint->getUrl());
    }

    /**
     * @param \Cabbage\Endpoint $endpoint
     * @param \Cabbage\Document $document
     *
     * @return \Cabbage\Http\Response
     */
    public function index(Endpoint $endpoint, Document $document): Response
    {
        $url = "{$endpoint->getUrl()}/{$document->type}/{$document->id}";

        $request = new Request(
            $this->documentSerializer->serialize($document),
            [
                'Content-Type' => 'application/json',
            ]
        );

        $response = $this->client->post($request, $url);

        if ($response->status !== 201) {
            throw new RuntimeException("Invalid response status {$response->status}");
        }

        return $response;
    }

    /**
     * @param \Cabbage\Endpoint $endpoint
     * @param string $type
     * @param array|array[] $query
     *
     * @return \Cabbage\Http\Response
     */
    public function find(Endpoint $endpoint, string $type, array $query): Response
    {
        $url = "{$endpoint->getUrl()}/{$type}/_search";

        $request = new Request(
            (string)json_encode($query),
            [
                'Content-Type' => 'application/json',
            ]
        );

        $response = $this->client->get($request, $url);

        if ($response->status !== 200) {
            throw new RuntimeException("Invalid response status {$response->status}");
        }

        return $response;
    }

    /**
     * @param \Cabbage\Endpoint $endpoint
     *
     * @return \Cabbage\Http\Response
     */
    public function flush(Endpoint $endpoint): Response
    {
        $url = "{$endpoint->getUrl()}/_flush";
        $request = new Request();

        return $this->client->post($request, $url);
    }
}
