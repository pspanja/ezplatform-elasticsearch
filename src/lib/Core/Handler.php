<?php

declare(strict_types=1);

namespace Cabbage\Core;

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
     * @var \Cabbage\Core\Indexer
     */
    private $indexer;

    /**
     * @var \Cabbage\Core\Searcher
     */
    private $searcher;

    /**
     * @param \Cabbage\Core\Indexer $indexer
     * @param \Cabbage\Core\Searcher $searcher
     */
    public function __construct(Indexer $indexer, Searcher $searcher)
    {
        $this->indexer = $indexer;
        $this->searcher = $searcher;
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
        return $this->searcher->findContent($query, $languageFilter);
    }

    /**
     * {@inheritdoc}
     */
    public function findSingle(Criterion $filter, array $languageFilter = []): ContentInfo
    {
        return $this->searcher->findSingle($filter, $languageFilter);
    }

    /**
     * {@inheritdoc}
     */
    public function findLocations(LocationQuery $query, array $languageFilter = []): SearchResult
    {
        return $this->searcher->findLocations($query, $languageFilter);
    }

    /**
     * {@inheritdoc}
     */
    public function suggest($prefix, $fieldPaths = [], $limit = 10, ?Criterion $filter = null): void
    {
        $this->searcher->suggest($prefix, $fieldPaths, $limit, $filter);
    }

    /**
     * {@inheritdoc}
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     */
    public function indexContent(Content $content): void
    {
        $this->indexer->indexContent($content);
    }

    /**
     * {@inheritdoc}
     */
    public function deleteContent($contentId, $versionId = null): void
    {
        $this->indexer->deleteContent($contentId, $versionId);
    }

    public function indexLocation(Location $location): void
    {
        $this->indexer->indexLocation($location);
    }

    /**
     * {@inheritdoc}
     */
    public function deleteLocation($locationId, $contentId): void
    {
        $this->indexer->deleteLocation($locationId, $contentId);
    }

    public function purgeIndex(): void
    {
        $this->indexer->purgeIndex();
    }

    /**
     * @param \eZ\Publish\SPI\Persistence\Content[] $contentItems
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     */
    public function bulkIndexContent(array $contentItems): void
    {
        $this->indexer->bulkIndexContent($contentItems);
    }

    public function flush(): void
    {
        $this->indexer->flush();
    }

    public function refresh(): void
    {
        $this->indexer->refresh();
    }
}
