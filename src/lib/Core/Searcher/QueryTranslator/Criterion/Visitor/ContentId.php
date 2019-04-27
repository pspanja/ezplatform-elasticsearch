<?php

declare(strict_types=1);

namespace Cabbage\Core\Searcher\QueryTranslator\Criterion\Visitor;

use Cabbage\Core\Searcher\QueryTranslator\Criterion\Visitor;
use Cabbage\Core\Searcher\QueryTranslator\Criterion\Converter;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\ContentId as ContentIdCriterion;

/**
 * Visits ContentId criterion.
 *
 * @see \eZ\Publish\API\Repository\Values\Content\Query\Criterion\ContentId
 */
final class ContentId extends Visitor
{
    public function accept(Criterion $criterion): bool
    {
        return $criterion instanceof ContentIdCriterion;
    }

    public function visit(Criterion $criterion, Converter $converter): array
    {
        return [
            'term' => [
                'content_id_identifier' => $criterion->value[0],
            ],
        ];
    }
}
