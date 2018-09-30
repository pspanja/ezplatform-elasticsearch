<?php

declare(strict_types=1);

namespace Cabbage;

use eZ\Publish\API\Repository\Values\Content\LocationQuery;
use eZ\Publish\API\Repository\Values\Content\Query;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion;
use eZ\Publish\API\Repository\Values\Content\Search\SearchHit;
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
     * @var \Cabbage\DocumentRouter
     */
    private $documentRouter;

    /**
     * @param \Cabbage\Gateway $gateway
     * @param \Cabbage\DocumentMapper $documentMapper
     * @param \Cabbage\DocumentRouter $documentRouter
     */
    public function __construct(Gateway $gateway, DocumentMapper $documentMapper, DocumentRouter $documentRouter)
    {
        $this->gateway = $gateway;
        $this->documentMapper = $documentMapper;
        $this->documentRouter = $documentRouter;
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
        $endpoint = Endpoint::fromDsn('http://localhost:9200/index');
        $response = $this->gateway->find($endpoint, 'test', 'test_string', 'value');

        $body = json_decode($response->body);
        $searchHits = [];

        foreach ($body->hits->hits as $hit) {
            $searchHits[] = new SearchHit(['valueObject' => $hit]);
        }

        return new SearchResult([
            'searchHits' => $searchHits,
            'totalCount' => $body->hits->total,
        ]);
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
        $document = $this->documentMapper->map();
        $endpoint = $this->documentRouter->match($document);

        $this->gateway->index($endpoint, $document);
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
