<?php

declare(strict_types=1);

namespace Cabbage;

use eZ\Publish\API\Repository\Values\Content\LocationQuery;
use eZ\Publish\API\Repository\Values\Content\Query;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion;
use eZ\Publish\API\Repository\Values\Content\Search\SearchResult;
use eZ\Publish\SPI\Persistence\Content;
use eZ\Publish\SPI\Persistence\Content\ContentInfo;
use eZ\Publish\SPI\Persistence\Content\Location;
use eZ\Publish\SPI\Search\Capable;
use eZ\Publish\SPI\Search\Handler as HandlerInterface;

final class Handler implements HandlerInterface, Capable
{
    /**
     * @var \Cabbage\Gateway
     */
    private $gateway;

    /**
     * @var \Cabbage\DocumentMapper
     */
    private $documentMapper;

    /**
     * @var \Cabbage\DocumentBulkSerializer
     */
    private $documentBulkSerializer;

    /**
     * @var \Cabbage\QueryTranslator
     */
    private $queryTranslator;

    /**
     * @var \Cabbage\QueryRouter
     */
    private $queryRouter;

    /**
     * @var \Cabbage\ResultExtractor
     */
    private $resultExtractor;

    /**
     * @param \Cabbage\Gateway $gateway
     * @param \Cabbage\DocumentMapper $documentMapper
     * @param \Cabbage\DocumentBulkSerializer $documentBulkSerializer
     * @param \Cabbage\QueryTranslator $queryTranslator
     * @param \Cabbage\QueryRouter $queryRouter
     * @param \Cabbage\ResultExtractor $resultExtractor
     */
    public function __construct(
        Gateway $gateway,
        DocumentMapper $documentMapper,
        DocumentBulkSerializer $documentBulkSerializer,
        QueryTranslator $queryTranslator,
        QueryRouter $queryRouter,
        ResultExtractor $resultExtractor
    ) {
        $this->gateway = $gateway;
        $this->documentMapper = $documentMapper;
        $this->documentBulkSerializer = $documentBulkSerializer;
        $this->queryTranslator = $queryTranslator;
        $this->queryRouter = $queryRouter;
        $this->resultExtractor = $resultExtractor;
    }

    /**
     * {@inheritdoc}
     */
    public function supports($capabilityFlag): bool
    {
        // TODO: Implement supports() method.
    }

    /**
     * {@inheritdoc}
     */
    public function findContent(Query $query, array $languageFilter = []): SearchResult
    {
        $endpoint = $this->queryRouter->match($query);
        $gatewayQuery = $this->queryTranslator->translate($query);

        $response = $this->gateway->find($endpoint, 'temporary', $gatewayQuery);

        return $this->resultExtractor->extract($response);
    }

    /**
     * {@inheritdoc}
     */
    public function findSingle(Criterion $filter, array $languageFilter = []): ContentInfo
    {
        // TODO: Implement findSingle() method.
    }

    /**
     * {@inheritdoc}
     */
    public function findLocations(LocationQuery $query, array $languageFilter = []): SearchResult
    {
        // TODO: Implement findLocations() method.
    }

    /**
     * {@inheritdoc}
     */
    public function suggest($prefix, $fieldPaths = [], $limit = 10, ?Criterion $filter = null): void
    {
        // TODO: Implement suggest() method.
    }

    /**
     * {@inheritdoc}
     */
    public function indexContent(Content $content): void
    {
        $documents = $this->documentMapper->map();

        $payload = $this->documentBulkSerializer->serialize($documents);

        $this->gateway->bulkIndex(Endpoint::fromDsn('http://localhost:9200/index'), $payload);
    }

    /**
     * {@inheritdoc}
     */
    public function deleteContent($contentId, $versionId = null): void
    {
        // TODO: Implement deleteContent() method.
    }

    /**
     * {@inheritdoc}
     */
    public function indexLocation(Location $location): void
    {
        // TODO: Implement indexLocation() method.
    }

    /**
     * {@inheritdoc}
     */
    public function deleteLocation($locationId, $contentId): void
    {
        // TODO: Implement deleteLocation() method.
    }

    /**
     * {@inheritdoc}
     */
    public function purgeIndex(): void
    {
        // TODO: Implement purgeIndex() method.
    }
}
