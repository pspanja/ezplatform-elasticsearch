<?php

declare(strict_types=1);

namespace Cabbage\Core;

use Cabbage\DocumentBulkSerializer;
use Cabbage\DocumentMapper;
use Cabbage\SPI\Endpoint;
use eZ\Publish\API\Repository\Values\Content\LocationQuery;
use eZ\Publish\API\Repository\Values\Content\Query;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion;
use eZ\Publish\API\Repository\Values\Content\Search\SearchResult;
use eZ\Publish\SPI\Persistence\Content;
use eZ\Publish\SPI\Persistence\Content\ContentInfo;
use eZ\Publish\SPI\Persistence\Content\Location;
use eZ\Publish\SPI\Search\Capable;
use eZ\Publish\SPI\Search\Handler as HandlerInterface;
use RuntimeException;

final class Handler implements HandlerInterface, Capable
{
    /**
     * @var \Cabbage\Core\Gateway
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
     * @var \Cabbage\Core\QueryTranslator
     */
    private $queryTranslator;

    /**
     * @var \Cabbage\Core\QueryRouter
     */
    private $queryRouter;

    /**
     * @var \Cabbage\Core\ResultExtractor
     */
    private $resultExtractor;

    /**
     * @param \Cabbage\Core\Gateway $gateway
     * @param \Cabbage\DocumentMapper $documentMapper
     * @param \Cabbage\DocumentBulkSerializer $documentBulkSerializer
     * @param \Cabbage\Core\QueryTranslator $queryTranslator
     * @param \Cabbage\Core\QueryRouter $queryRouter
     * @param \Cabbage\Core\ResultExtractor $resultExtractor
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
        throw new RuntimeException('Not implemented');
    }

    /**
     * {@inheritdoc}
     */
    public function findContent(Query $query, array $languageFilter = []): SearchResult
    {
        $endpoint = $this->queryRouter->match($query);
        $gatewayQuery = $this->queryTranslator->translateContentQuery($query);

        $data = $this->gateway->find($endpoint, $gatewayQuery);

        return $this->resultExtractor->extract($data);
    }

    /**
     * {@inheritdoc}
     */
    public function findSingle(Criterion $filter, array $languageFilter = []): ContentInfo
    {
        throw new RuntimeException('Not implemented');
    }

    /**
     * {@inheritdoc}
     */
    public function findLocations(LocationQuery $query, array $languageFilter = []): SearchResult
    {
        $endpoint = $this->queryRouter->match($query);
        $gatewayQuery = $this->queryTranslator->translateLocationQuery($query);

        $data = $this->gateway->find($endpoint, $gatewayQuery);

        return $this->resultExtractor->extract($data);
    }

    /**
     * {@inheritdoc}
     */
    public function suggest($prefix, $fieldPaths = [], $limit = 10, ?Criterion $filter = null): void
    {
        throw new RuntimeException('Not implemented');
    }

    public function indexContent(Content $content): void
    {
        $documents = $this->documentMapper->map($content);

        $payload = $this->documentBulkSerializer->serialize($documents);

        $this->gateway->bulkIndex(Endpoint::fromDsn('http://localhost:9200/index'), $payload);
    }

    /**
     * {@inheritdoc}
     */
    public function deleteContent($contentId, $versionId = null): void
    {
        throw new RuntimeException('Not implemented');
    }

    public function indexLocation(Location $location): void
    {
        throw new RuntimeException('Not implemented');
    }

    /**
     * {@inheritdoc}
     */
    public function deleteLocation($locationId, $contentId): void
    {
        throw new RuntimeException('Not implemented');
    }

    public function purgeIndex(): void
    {
        throw new RuntimeException('Not implemented');
    }
}
