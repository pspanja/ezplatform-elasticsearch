<?php

declare(strict_types=1);

namespace Cabbage\Core\Searcher\QueryTranslator\Criterion\Visitor;

use Cabbage\API\Query\Criterion\RawField as RawFieldCriterion;
use Cabbage\Core\Searcher\QueryTranslator\Criterion\Visitor;
use Cabbage\Core\Searcher\QueryTranslator\Criterion\Converter;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion;

/**
 * Visits RawField criterion.
 *
 * @see \Cabbage\API\Query\Criterion\RawField
 */
final class RawField extends Visitor
{
    public function accept(Criterion $criterion): bool
    {
        return $criterion instanceof RawFieldCriterion;
    }

    public function visit(Criterion $criterion, Converter $converter): array
    {
        return [
            'term' => [
                $criterion->target => $criterion->value[0],
            ],
        ];
    }
}
