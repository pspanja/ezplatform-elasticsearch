<?php

declare(strict_types=1);

namespace Cabbage\Core\Query\Translator\CriterionVisitor;

use Cabbage\Core\Query\Translator\CriterionVisitor;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\LogicalAnd as LogicalAndCriterion;

final class LogicalAnd extends CriterionVisitor
{
    public function accept(Criterion $criterion): bool
    {
        return $criterion instanceof LogicalAndCriterion;
    }

    public function visit(Criterion $criterion, CriterionVisitor $subVisitor = null): array
    {
        \assert($subVisitor instanceof CriterionVisitor);
        \assert($criterion instanceof LogicalAndCriterion);

        $criteria = array_map(
            function ($value) use ($subVisitor) {
                return $subVisitor->visit($value, $subVisitor);
            },
            $criterion->criteria
        );

        return ['bool' => ['must' => [$criteria]]];
    }
}
