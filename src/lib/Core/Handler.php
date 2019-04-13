<?php

declare(strict_types=1);

namespace Cabbage\Core;

use Cabbage\Core\Query\TargetResolver;
use Cabbage\Core\Query\Translator;
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
     * @var \Cabbage\Core\Gateway
     */
    private $gateway;

    /**
     * @var \Cabbage\Core\Query\Translator
     */
    private $queryTranslator;

    /**
     * @var \Cabbage\Core\Query\TargetResolver
     */
    private $targetResolver;

    /**
     * @var \Cabbage\Core\ResultExtractor
     */
    private $resultExtractor;

    /**
     * @param \Cabbage\Core\Indexer $indexer
     * @param \Cabbage\Core\Gateway $gateway
     * @param \Cabbage\Core\Query\Translator $queryTranslator
     * @param \Cabbage\Core\Query\TargetResolver $targetResolver
     * @param \Cabbage\Core\ResultExtractor $resultExtractor
     */
    public function __construct(
        Indexer $indexer,
        Gateway $gateway,
        Translator $queryTranslator,
        TargetResolver $targetResolver,
        ResultExtractor $resultExtractor
    ) {
        $this->indexer = $indexer;
        $this->gateway = $gateway;
        $this->queryTranslator = $queryTranslator;
        $this->targetResolver = $targetResolver;
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
        return
            $this->resultExtractor->extract(
                $this->gateway->find(
                    $this->targetResolver->resolve($query),
                    $this->queryTranslator->translateContentQuery($query)
                )
            );
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
        return
            $this->resultExtractor->extract(
                $this->gateway->find(
                    $this->targetResolver->resolve($query),
                    $this->queryTranslator->translateLocationQuery($query)
                )
            );
    }

    /**
     * {@inheritdoc}
     */
    public function suggest($prefix, $fieldPaths = [], $limit = 10, ?Criterion $filter = null): void
    {
        throw new RuntimeException('Not implemented');
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
