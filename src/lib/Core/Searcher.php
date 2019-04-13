<?php

declare(strict_types=1);

namespace Cabbage\Core;

use Cabbage\Core\Searcher\Query\TargetResolver;
use Cabbage\Core\Searcher\Query\Translator;
use Cabbage\Core\Searcher\ResultExtractor;
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
     * @var \Cabbage\Core\Gateway
     */
    private $gateway;

    /**
     * @var \Cabbage\Core\Searcher\Query\Translator
     */
    private $queryTranslator;

    /**
     * @var \Cabbage\Core\Searcher\Query\TargetResolver
     */
    private $targetResolver;

    /**
     * @var \Cabbage\Core\Searcher\ResultExtractor
     */
    private $resultExtractor;

    /**
     * @param \Cabbage\Core\Gateway $gateway
     * @param \Cabbage\Core\Searcher\Query\Translator $queryTranslator
     * @param \Cabbage\Core\Searcher\Query\TargetResolver $targetResolver
     * @param \Cabbage\Core\Searcher\ResultExtractor $resultExtractor
     */
    public function __construct(
        Gateway $gateway,
        Translator $queryTranslator,
        TargetResolver $targetResolver,
        ResultExtractor $resultExtractor
    ) {
        $this->gateway = $gateway;
        $this->queryTranslator = $queryTranslator;
        $this->targetResolver = $targetResolver;
        $this->resultExtractor = $resultExtractor;
    }

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

    public function findSingle(Criterion $filter, array $languageFilter = []): ContentInfo
    {
        throw new RuntimeException('Not implemented');
    }

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

    public function suggest($prefix, $fieldPaths = [], $limit = 10, ?Criterion $filter = null): void
    {
        throw new RuntimeException('Not implemented');
    }
}
