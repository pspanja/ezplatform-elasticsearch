<?php

declare(strict_types=1);

namespace Cabbage\Core;

use Cabbage\Core\Indexer\DocumentBuilder;
use Cabbage\Core\Indexer\DocumentIndexResolver;
use Cabbage\Core\Indexer\DocumentSerializer;
use Cabbage\Core\Indexer\Gateway;
use Cabbage\SPI\Document;
use Cabbage\SPI\Indexer as SPIIndexer;
use eZ\Publish\SPI\Persistence\Content;
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
     * @var \Cabbage\Core\Indexer\DocumentIndexResolver
     */
    private $documentIndexResolver;

    /**
     * @var \Cabbage\Core\Indexer\DocumentSerializer
     */
    private $documentSerializer;

    /**
     * @var \Cabbage\Core\Cluster
     */
    private $cluster;

    /**
     * @param \Cabbage\Core\Indexer\Gateway $gateway
     * @param \Cabbage\Core\Indexer\DocumentBuilder $documentBuilder
     * @param \Cabbage\Core\Indexer\DocumentIndexResolver $documentIndexResolver
     * @param \Cabbage\Core\Indexer\DocumentSerializer $documentSerializer
     * @param \Cabbage\Core\Cluster $cluster
     */
    public function __construct(
        Gateway $gateway,
        DocumentBuilder $documentBuilder,
        DocumentIndexResolver $documentIndexResolver,
        DocumentSerializer $documentSerializer,
        Cluster $cluster
    ) {
        $this->gateway = $gateway;
        $this->documentBuilder = $documentBuilder;
        $this->documentIndexResolver = $documentIndexResolver;
        $this->documentSerializer = $documentSerializer;
        $this->cluster = $cluster;
    }

    /**
     * {@inheritDoc}
     */
    public function deleteContent($contentId, $versionId = null): void
    {
        throw new RuntimeException('Not implemented');
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
            $this->cluster->selectCoordinatingNode()
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
            $this->cluster->selectCoordinatingNode(),
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
        $data = [
            'index' => [
                '_index' => $document->index,
                '_id' => $document->id,
            ],
        ];

        return json_encode($data, JSON_THROW_ON_ERROR);
    }

    public function flush(): void
    {
        $this->gateway->flush(
            $this->cluster->selectCoordinatingNode()
        );
    }

    public function refresh(): void
    {
        $this->gateway->refresh(
            $this->cluster->selectCoordinatingNode()
        );
    }
}
