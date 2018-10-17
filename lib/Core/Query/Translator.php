<?php

declare(strict_types=1);

namespace Cabbage\Core\Query;

use Cabbage\API\Query\Criterion\DocumentType;
use Cabbage\Core\Query\Translator\CriterionConverter\DocumentType as DocumentTypeCriterionConverter;
use eZ\Publish\API\Repository\Values\Content\LocationQuery;
use eZ\Publish\API\Repository\Values\Content\Query;
use RuntimeException;

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
     * @var \Cabbage\Core\Query\Translator\CriterionConverter\DocumentType
     */
    private $converter;

    public function __construct(DocumentTypeCriterionConverter $converter)
    {
        $this->converter = $converter;
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\Query $query
     *
     * @return array|array[]
     */
    public function translateContentQuery(Query $query): array
    {
        if (!$query->filter instanceof DocumentType) {
            throw new RuntimeException('Unknown criterion');
        }

        return [
            'query' => $this->converter->convert($query->filter),
        ];
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\LocationQuery $query
     *
     * @return array|array[]
     */
    public function translateLocationQuery(LocationQuery $query): array
    {
        if (!$query->filter instanceof DocumentType) {
            throw new RuntimeException('Unknown criterion');
        }

        return [
            'query' => $this->converter->convert($query->filter),
        ];
    }
}
