<?php

declare(strict_types=1);

namespace Cabbage\Core;

use Cabbage\Core\Searcher\Gateway;
use Cabbage\Core\Searcher\LanguageFilterTargetResolver;
use Cabbage\Core\Searcher\QueryTranslator;
use Cabbage\Core\Searcher\ResultExtractor;
use Cabbage\SPI\LanguageFilter;
use Cabbage\SPI\Searcher as SPISearcher;
use eZ\Publish\API\Repository\Values\Content\LocationQuery;
use eZ\Publish\API\Repository\Values\Content\Query;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion;
use eZ\Publish\API\Repository\Values\Content\Search\SearchResult;
use eZ\Publish\SPI\Persistence\Content\ContentInfo;
use RuntimeException;

final class Searcher extends SPISearcher
{
    /**
     * @var \Cabbage\Core\Searcher\Gateway
     */
    private $gateway;

    /**
     * @var \Cabbage\Core\Searcher\LanguageFilterTargetResolver
     */
    private $languageFilterTargetResolver;

    /**
     * @var \Cabbage\Core\Searcher\QueryTranslator
     */
    private $queryTranslator;

    /**
     * @var \Cabbage\Core\Searcher\ResultExtractor
     */
    private $resultExtractor;

    /**
     * @var \Cabbage\Core\Cluster
     */
    private $cluster;

    /**
     * @param \Cabbage\Core\Searcher\Gateway $gateway
     * @param \Cabbage\Core\Searcher\LanguageFilterTargetResolver $languageFilterTargetResolver
     * @param \Cabbage\Core\Searcher\QueryTranslator $queryTranslator
     * @param \Cabbage\Core\Searcher\ResultExtractor $resultExtractor
     * @param \Cabbage\Core\Cluster $cluster
     */
    public function __construct(
        Gateway $gateway,
        LanguageFilterTargetResolver $languageFilterTargetResolver,
        QueryTranslator $queryTranslator,
        ResultExtractor $resultExtractor,
        Cluster $cluster
    ) {
        $this->gateway = $gateway;
        $this->languageFilterTargetResolver = $languageFilterTargetResolver;
        $this->queryTranslator = $queryTranslator;
        $this->resultExtractor = $resultExtractor;
        $this->cluster = $cluster;
    }

    public function findContent(Query $query, LanguageFilter $languageFilter): SearchResult
    {
        $data = $this->gateway->find(
            $this->cluster->selectCoordinatingNode(),
            $this->languageFilterTargetResolver->resolve($languageFilter),
            $this->queryTranslator->translateContentQuery($query)
        );

        return $this->resultExtractor->extract($data);
    }

    public function findSingle(Criterion $filter, LanguageFilter $languageFilter): ContentInfo
    {
        throw new RuntimeException('Not implemented');
    }

    public function findLocations(LocationQuery $query, LanguageFilter $languageFilter): SearchResult
    {
        $data = $this->gateway->find(
            $this->cluster->selectCoordinatingNode(),
            $this->languageFilterTargetResolver->resolve($languageFilter),
            $this->queryTranslator->translateLocationQuery($query)
        );

        return $this->resultExtractor->extract($data);
    }

    public function suggest($prefix, $fieldPaths = [], $limit = 10, ?Criterion $filter = null): void
    {
        throw new RuntimeException('Not implemented');
    }
}
