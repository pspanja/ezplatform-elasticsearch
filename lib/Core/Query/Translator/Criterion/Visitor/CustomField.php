<?php

declare(strict_types=1);

namespace Cabbage\Core\Query\Translator\Criterion\Visitor;

use Cabbage\API\Query\Criterion\CustomField as CustomFieldCriterion;
use Cabbage\Core\Query\Translator\Criterion\Visitor;
use Cabbage\Core\Query\Translator\Criterion\VisitorDispatcher;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion;

final class CustomField extends Visitor
{
    public function accept(Criterion $criterion): bool
    {
        return $criterion instanceof CustomFieldCriterion;
    }

    public function visit(Criterion $criterion, VisitorDispatcher $dispatcher): array
    {
        return [
            'term' => [
                $criterion->target => $criterion->value[0],
            ],
        ];
    }
}
