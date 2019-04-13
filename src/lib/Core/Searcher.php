<?php

declare(strict_types=1);

namespace Cabbage\Core;

use Cabbage\Core\Query\TargetResolver;
use Cabbage\Core\Query\Translator;
use eZ\Publish\API\Repository\Values\Content\LocationQuery;
use eZ\Publish\API\Repository\Values\Content\Query;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion;
use eZ\Publish\API\Repository\Values\Content\Search\SearchResult;
use eZ\Publish\SPI\Persistence\Content\ContentInfo;
use RuntimeException;

final class Searcher
{
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
     * @param \Cabbage\Core\Gateway $gateway
     * @param \Cabbage\Core\Query\Translator $queryTranslator
     * @param \Cabbage\Core\Query\TargetResolver $targetResolver
     * @param \Cabbage\Core\ResultExtractor $resultExtractor
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

    /**
     * @see \eZ\Publish\SPI\Search\Handler::findContent()
     *
     * @param \eZ\Publish\API\Repository\Values\Content\Query $query
     * @param array $languageFilter
     *
     * @return \eZ\Publish\API\Repository\Values\Content\Search\SearchResult
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
     * @see \eZ\Publish\SPI\Search\Handler::findSingle()
     *
     * @param \eZ\Publish\API\Repository\Values\Content\Query\Criterion $filter
     * @param array $languageFilter
     *
     * @return \eZ\Publish\SPI\Persistence\Content\ContentInfo
     */
    public function findSingle(Criterion $filter, array $languageFilter = []): ContentInfo
    {
        throw new RuntimeException('Not implemented');
    }

    /**
     * @see \eZ\Publish\SPI\Search\Handler::findLocations()
     *
     * @param \eZ\Publish\API\Repository\Values\Content\LocationQuery $query
     * @param array $languageFilter
     *
     * @return \eZ\Publish\API\Repository\Values\Content\Search\SearchResult
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
     * @see \eZ\Publish\SPI\Search\Handler::suggest()
     *
     * @param $prefix
     * @param array $fieldPaths
     * @param int $limit
     * @param \eZ\Publish\API\Repository\Values\Content\Query\Criterion|null $filter
     */
    public function suggest($prefix, $fieldPaths = [], $limit = 10, ?Criterion $filter = null): void
    {
        throw new RuntimeException('Not implemented');
    }
}
