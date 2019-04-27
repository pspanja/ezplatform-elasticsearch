<?php

declare(strict_types=1);

namespace Cabbage\Core;

use Cabbage\Core\Indexer\Document\Builder;
use Cabbage\Core\Indexer\Document\Serializer;
use Cabbage\Core\Indexer\Gateway;
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
     * @param \Cabbage\Core\Indexer\Gateway $gateway
     * @param \Cabbage\Core\Indexer\Document\Builder $documentBuilder
     * @param \Cabbage\Core\Indexer\Document\Serializer $documentSerializer
     */
    public function __construct(
        Gateway $gateway,
        Builder $documentBuilder,
        Serializer $documentSerializer
    ) {
        $this->gateway = $gateway;
        $this->documentBuilder = $documentBuilder;
        $this->documentSerializer = $documentSerializer;
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
            $payload .= $this->documentSerializer->serialize(
                $this->documentBuilder->build($content)
            );
        }

        $this->gateway->index(
            new Index(
                Node::fromDsn('http://localhost:9200'),
                'index'
            ),
            $payload
        );
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
