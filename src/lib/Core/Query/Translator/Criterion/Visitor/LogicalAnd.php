<?php

declare(strict_types=1);

namespace Cabbage\Core\Query\Translator\Criterion\Visitor;

use Cabbage\Core\Query\Translator\Criterion\Visitor;
use Cabbage\Core\Query\Translator\Criterion\Converter;
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
        \assert($criterion instanceof LogicalAndCriterion);

        $criteria = array_map(
            function ($value) use ($converter) {
                return $converter->convert($value);
            },
            $criterion->criteria
        );

        return ['bool' => ['must' => [$criteria]]];
    }
}
