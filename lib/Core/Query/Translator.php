<?php

declare(strict_types=1);

namespace Cabbage\Core\Query;

use Cabbage\SPI\Document;
use eZ\Publish\API\Repository\Values\Content\LocationQuery;
use eZ\Publish\API\Repository\Values\Content\Query;

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
     * @param \eZ\Publish\API\Repository\Values\Content\Query $query
     *
     * @return array|array[]
     */
    public function translateContentQuery(Query $query): array
    {
        return [
            'query' => [
                'bool' => [
                    'must' => [
                        [
                            'term' => [
                                'type' => Document::TypeContent,
                            ],
                        ],
                        [
                            'term' => [
                                'test_string' => 'value',
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\LocationQuery $query
     *
     * @return array|array[]
     */
    public function translateLocationQuery(LocationQuery $query): array
    {
        return [
            'query' => [
                'bool' => [
                    'must' => [
                        [
                            'term' => [
                                'type' => Document::TypeLocation,
                            ],
                        ],
                        [
                            'term' => [
                                'test_string' => 'value',
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }
}
