<?php

declare(strict_types=1);

namespace Cabbage\Core;

use Cabbage\Core\Indexer\DocumentBuilder;
use Cabbage\Core\Indexer\DestinationResolver;
use Cabbage\Core\Indexer\DocumentSerializer;
use Cabbage\Core\Indexer\Gateway;
use Cabbage\SPI\Document;
use Cabbage\SPI\Index;
use Cabbage\SPI\Indexer as SPIIndexer;
use Cabbage\SPI\Node;
use eZ\Publish\SPI\Persistence\Content;
use eZ\Publish\SPI\Persistence\Content\Location;
use RuntimeException;

final class Indexer extends SPIIndexer
{
    /**
     * @var \Cabbage\Core\Indexer\Gateway
     */
    private $gateway;

    /**
     * @var \Cabbage\Core\Indexer\DocumentBuilder
     */
    private $documentBuilder;

    /**
     * @var \Cabbage\Core\Indexer\DocumentSerializer
     */
    private $documentSerializer;

    /**
     * @var \Cabbage\Core\Indexer\DestinationResolver
     */
    private $destinationResolver;

    /**
     * @param \Cabbage\Core\Indexer\Gateway $gateway
     * @param \Cabbage\Core\Indexer\DocumentBuilder $documentBuilder
     * @param \Cabbage\Core\Indexer\DocumentSerializer $documentSerializer
     * @param \Cabbage\Core\Indexer\DestinationResolver $destinationResolver
     */
    public function __construct(
        Gateway $gateway,
        DocumentBuilder $documentBuilder,
        DocumentSerializer $documentSerializer,
        DestinationResolver $destinationResolver
    ) {
        $this->gateway = $gateway;
        $this->documentBuilder = $documentBuilder;
        $this->documentSerializer = $documentSerializer;
        $this->destinationResolver = $destinationResolver;
    }

    /**
     * {@inheritDoc}
     */
    public function deleteContent($contentId, $versionId = null): void
    {
        throw new RuntimeException('Not implemented');
    }

    public function indexLocation(Location $location): void
    {
        // Does nothing
    }

    /**
     * {@inheritDoc}
     */
    public function deleteLocation($locationId, $contentId): void
    {
        throw new RuntimeException('Not implemented');
    }

    public function purgeIndex(): void
    {
        $this->gateway->purge(Node::fromDsn('http://localhost:9200'));
    }

    /**
     * {@inheritDoc}
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     */
    public function bulkIndexContent(array $contentItems): void
    {
        $payload = '';

        foreach ($contentItems as $content) {
            $payload .= $this->getIndexingPayloadForContent($content);
        }

        $this->gateway->index(
            Node::fromDsn('http://localhost:9200'),
            $payload
        );
    }

    /**
     * @param \eZ\Publish\SPI\Persistence\Content $content
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     *
     * @return string
     */
    private function getIndexingPayloadForContent(Content $content): string
    {
        $payload = '';
        $documents = $this->documentBuilder->build($content);

        foreach ($documents as $document) {
            $actionAndMetaData = $this->getActionAndMetaData($document);
            $serializedDocument = $this->documentSerializer->serialize($document);

            $payload .= "{$actionAndMetaData}\n{$serializedDocument}\n";
        }

        return $payload;
    }

    /**
     * Generate action and metadata for the indexed Document.
     *
     * @param \Cabbage\SPI\Document $document
     *
     * @return string
     */
    private function getActionAndMetaData(Document $document): string
    {
        $index = $this->destinationResolver->resolve($document);

        $data = [
            'index' => [
                '_index' => $index->name,
                '_id' => $document->id,
            ],
        ];

        return json_encode($data, JSON_THROW_ON_ERROR);
    }

    public function flush(): void
    {
        $this->gateway->flush(Node::fromDsn('http://localhost:9200'));
    }

    public function refresh(): void
    {
        $this->gateway->refresh(Node::fromDsn('http://localhost:9200'));
    }
}
