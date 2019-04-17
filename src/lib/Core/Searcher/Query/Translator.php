<?php

declare(strict_types=1);

namespace Cabbage\Core\Searcher\Query;

use Cabbage\Core\Searcher\Query\Translator\Criterion\Converter;
use eZ\Publish\API\Repository\Values\Content\LocationQuery;
use eZ\Publish\API\Repository\Values\Content\Query;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\MatchAll;

/**
 * Translates eZ Platform Query instance to an array that can be can be
 * serialized to JSON that is valid Elasticsearch Query DSL.
 *
 * @see \eZ\Publish\API\Repository\Values\Content\Query
 * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl.html
 */
final class Translator
{
    /**
     * @var \Cabbage\Core\Searcher\Query\Translator\Criterion\Converter
     */
    private $criterionConverter;

    public function __construct(Converter $criterionConverter)
    {
        $this->criterionConverter = $criterionConverter;
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\Query $query
     *
     * @return array|array[]
     */
    public function translateContentQuery(Query $query): array
    {
        $must = $query->query ?? new MatchAll();
        $filter = $query->filter ?? new MatchAll();

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
     *
     * @return array|array[]
     */
    public function translateLocationQuery(LocationQuery $query): array
    {
        $must = $query->query ?? new MatchAll();
        $filter = $query->filter ?? new MatchAll();

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
}
