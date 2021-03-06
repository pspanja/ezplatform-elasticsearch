<?php

declare(strict_types=1);

namespace Cabbage\Core\Searcher\QueryTranslator\Criterion\Visitor;

use function assert;
use Cabbage\Core\Searcher\QueryTranslator\Criterion\Visitor;
use Cabbage\Core\Searcher\QueryTranslator\Criterion\Converter;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\LogicalAnd as LogicalAndCriterion;

/**
 * Visits LogicalAnd criterion.
 *
 * @see \eZ\Publish\API\Repository\Values\Content\Query\Criterion\LogicalAnd
 */
final class LogicalAnd extends Visitor
{
    public function accept(Criterion $criterion): bool
    {
        return $criterion instanceof LogicalAndCriterion;
    }

    public function visit(Criterion $criterion, Converter $converter): array
    {
        assert($criterion instanceof LogicalAndCriterion);

        $criteria = array_map(
            static function (Criterion $value) use ($converter): array {
                return $converter->convert($value);
            },
            $criterion->criteria
        );

        return [
            'bool' => [
                'must' => [$criteria],
            ],
        ];
    }
}
