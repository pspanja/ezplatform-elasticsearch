<?php

declare(strict_types=1);

namespace Cabbage\Core\Query\Translator\CriterionVisitor;

use Cabbage\API\Query\Criterion\CustomField as CustomFieldCriterion;
use Cabbage\Core\Query\Translator\CriterionVisitor;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion;

final class CustomField extends CriterionVisitor
{
    public function accept(Criterion $criterion): bool
    {
        return $criterion instanceof CustomFieldCriterion;
    }

    public function visit(Criterion $criterion, CriterionVisitor $subVisitor = null): array
    {
        return [
            'term' => [
                $criterion->target => $criterion->value[0],
            ],
        ];
    }
}
