<?php

declare(strict_types=1);

namespace Cabbage;

use eZ\Publish\API\Repository\Values\Content\Query;

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
     * @param \eZ\Publish\API\Repository\Values\Content\Query $query
     *
     * @return array|array[]
     */
    public function translate(Query $query): array
    {
        return [
            'query' => [
                'term' => [
                    'test_string' => 'value',
                ],
            ],
        ];
    }
}
