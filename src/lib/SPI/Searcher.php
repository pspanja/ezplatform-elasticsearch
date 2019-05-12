<?php

declare(strict_types=1);

namespace Cabbage\SPI;

use eZ\Publish\API\Repository\Values\Content\LocationQuery;
use eZ\Publish\API\Repository\Values\Content\Query;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion;
use eZ\Publish\API\Repository\Values\Content\Search\SearchResult;
use eZ\Publish\SPI\Persistence\Content\ContentInfo;

abstract class Searcher
{
    /**
     * @param \eZ\Publish\API\Repository\Values\Content\Query $query
     * @param \Cabbage\SPI\TranslationFilter $translationFilter
     *
     * @return \eZ\Publish\API\Repository\Values\Content\Search\SearchResult
     *@see \eZ\Publish\SPI\Search\Handler::findContent()
     *
     */
    abstract public function findContent(Query $query, TranslationFilter $translationFilter): SearchResult;

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\Query\Criterion $filter
     * @param \Cabbage\SPI\TranslationFilter $translationFilter
     *
     * @return \eZ\Publish\SPI\Persistence\Content\ContentInfo
     *@see \eZ\Publish\SPI\Search\Handler::findSingle()
     *
     */
    abstract public function findSingle(Criterion $filter, TranslationFilter $translationFilter): ContentInfo;

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\LocationQuery $query
     * @param \Cabbage\SPI\TranslationFilter $translationFilter
     *
     * @return \eZ\Publish\API\Repository\Values\Content\Search\SearchResult
     *@see \eZ\Publish\SPI\Search\Handler::findLocations()
     *
     */
    abstract public function findLocations(LocationQuery $query, TranslationFilter $translationFilter): SearchResult;

    /**
     * @see \eZ\Publish\SPI\Search\Handler::suggest()
     *
     * @param string $prefix
     * @param string[] $fieldPaths
     * @param int $limit
     * @param \eZ\Publish\API\Repository\Values\Content\Query\Criterion $filter
     */
    abstract public function suggest($prefix, $fieldPaths = [], $limit = 10, ?Criterion $filter = null): void;
}
