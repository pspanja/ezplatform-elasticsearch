<?php

declare(strict_types=1);

namespace Cabbage\Core\Searcher\Query\Translator\Criterion\Visitor;

use Cabbage\Core\Searcher\Query\Translator\Criterion\Visitor;
use Cabbage\Core\Searcher\Query\Translator\Criterion\Converter;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\LogicalOr as LogicalOrCriterion;

/**
 * Visits LogicalOr criterion.
 *
 * @see \eZ\Publish\API\Repository\Values\Content\Query\Criterion\LogicalOr
 */
final class LogicalOr extends Visitor
{
    public function accept(Criterion $criterion): bool
    {
        return $criterion instanceof LogicalOrCriterion;
    }

    public function visit(Criterion $criterion, Converter $converter): array
    {
        \assert($criterion instanceof LogicalOrCriterion);

        $criteria = array_map(
            function (Criterion $value) use ($converter): array {
                return $converter->convert($value);
            },
            $criterion->criteria
        );

        return [
            'bool' => [
                'should' => [$criteria],
                'minimum_should_match' => 1,
            ],
        ];
    }
}
