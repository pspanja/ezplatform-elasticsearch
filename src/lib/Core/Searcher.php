<?php

declare(strict_types=1);

namespace Cabbage\Core;

use Cabbage\Core\Searcher\Gateway;
use Cabbage\Core\Searcher\QueryTranslator;
use Cabbage\Core\Searcher\ResultExtractor;
use Cabbage\Core\Searcher\TargetResolver;
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
     * @var \Cabbage\Core\Searcher\QueryTranslator
     */
    private $queryTranslator;

    /**
     * @var \Cabbage\Core\Searcher\TargetResolver
     */
    private $targetResolver;

    /**
     * @var \Cabbage\Core\Searcher\ResultExtractor
     */
    private $resultExtractor;

    /**
     * @param \Cabbage\Core\Searcher\Gateway $gateway
     * @param \Cabbage\Core\Searcher\QueryTranslator $queryTranslator
     * @param \Cabbage\Core\Searcher\TargetResolver $targetResolver
     * @param \Cabbage\Core\Searcher\ResultExtractor $resultExtractor
     */
    public function __construct(
        Gateway $gateway,
        QueryTranslator $queryTranslator,
        TargetResolver $targetResolver,
        ResultExtractor $resultExtractor
    ) {
        $this->gateway = $gateway;
        $this->queryTranslator = $queryTranslator;
        $this->targetResolver = $targetResolver;
        $this->resultExtractor = $resultExtractor;
    }

    public function findContent(Query $query, LanguageFilter $languageFilter): SearchResult
    {
        $data = $this->gateway->find(
            $this->targetResolver->resolve($languageFilter),
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
            $this->targetResolver->resolve($languageFilter),
            $this->queryTranslator->translateLocationQuery($query)
        );

        return $this->resultExtractor->extract($data);
    }

    public function suggest($prefix, $fieldPaths = [], $limit = 10, ?Criterion $filter = null): void
    {
        throw new RuntimeException('Not implemented');
    }
}
