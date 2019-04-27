<?php

declare(strict_types=1);

namespace Cabbage\Core;

use Cabbage\Core\Indexer\Document\Builder;
use Cabbage\Core\Indexer\Document\DestinationResolver;
use Cabbage\Core\Indexer\Document\Serializer;
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
     * @var \Cabbage\Core\Indexer\Document\Builder
     */
    private $documentBuilder;

    /**
     * @var \Cabbage\Core\Indexer\Document\Serializer
     */
    private $documentSerializer;

    /**
     * @var \Cabbage\Core\Indexer\Document\DestinationResolver
     */
    private $destinationResolver;

    /**
     * @param \Cabbage\Core\Indexer\Gateway $gateway
     * @param \Cabbage\Core\Indexer\Document\Builder $documentBuilder
     * @param \Cabbage\Core\Indexer\Document\Serializer $documentSerializer
     * @param \Cabbage\Core\Indexer\Document\DestinationResolver $destinationResolver
     */
    public function __construct(
        Gateway $gateway,
        Builder $documentBuilder,
        Serializer $documentSerializer,
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
        $this->gateway->purge(
            new Index(
                Node::fromDsn('http://localhost:9200'),
                'index'
            )
        );
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
            new Index(
                Node::fromDsn('http://localhost:9200'),
                'index'
            ),
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
        $this->gateway->flush(
            new Index(
                Node::fromDsn('http://localhost:9200'),
                'index'
            )
        );
    }

    public function refresh(): void
    {
        $this->gateway->refresh(
            new Index(
                Node::fromDsn('http://localhost:9200'),
                'index'
            )
        );
    }
}
