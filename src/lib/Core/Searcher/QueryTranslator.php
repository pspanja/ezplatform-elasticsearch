<?php

declare(strict_types=1);

namespace Cabbage\Core\Searcher;

use Cabbage\API\Query\Criterion\DocumentType;
use Cabbage\API\Query\Criterion\TranslationResolver;
use Cabbage\Core\Indexer\DocumentBuilder;
use Cabbage\Core\Searcher\QueryTranslator\Criterion\Converter;
use Cabbage\SPI\TranslationFilter;
use eZ\Publish\API\Repository\Values\Content\LocationQuery;
use eZ\Publish\API\Repository\Values\Content\Query;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\LogicalAnd;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\MatchAll;

/**
 * Translates eZ Platform Query instance to an array that can be can be
 * serialized to JSON that is valid Elasticsearch Query DSL.
 *
 * @see \eZ\Publish\API\Repository\Values\Content\Query
 * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl.html
 */
final class QueryTranslator
{
    /**
     * @var \Cabbage\Core\Searcher\QueryTranslator\Criterion\Converter
     */
    private $criterionConverter;

    public function __construct(Converter $criterionConverter)
    {
        $this->criterionConverter = $criterionConverter;
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\Query $query
     * @param \Cabbage\SPI\TranslationFilter $translationFilter
     *
     * @return array|array[]
     */
    public function translateContentQuery(Query $query, TranslationFilter $translationFilter): array
    {
        $must = $query->query ?? new MatchAll();
        $filter = $this->getFilterCriteria($query, $translationFilter, DocumentBuilder::TypeContent);

        return [
            'query' => [
                'bool' => [
                    'must' => $this->criterionConverter->convert($must),
                    'filter' => $this->criterionConverter->convert($filter),
                ],
            ],
            'from' => $query->offset,
            'size' => $query->limit,
        ];
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\LocationQuery $query
     * @param \Cabbage\SPI\TranslationFilter $translationFilter
     *
     * @return array|array[]
     */
    public function translateLocationQuery(LocationQuery $query, TranslationFilter $translationFilter): array
    {
        $must = $query->query ?? new MatchAll();
        $filter = $this->getFilterCriteria($query, $translationFilter, DocumentBuilder::TypeLocation);

        return [
            'query' => [
                'bool' => [
                    'must' => $this->criterionConverter->convert($must),
                    'filter' => $this->criterionConverter->convert($filter),
                ],
            ],
            'from' => $query->offset,
            'size' => $query->limit,
        ];
    }

    private function getFilterCriteria(
        Query $query,
        TranslationFilter $translationFilter,
        string $documentTypeIdentifier
    ): Criterion {
        $criteria = [
            new TranslationResolver($translationFilter),
            new DocumentType($documentTypeIdentifier),
        ];

        if ($query->filter instanceof Criterion) {
            $criteria[] = $query->filter;
        }

        return new LogicalAnd($criteria);
    }
}
