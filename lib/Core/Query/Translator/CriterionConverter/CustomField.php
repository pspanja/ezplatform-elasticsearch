<?php

declare(strict_types=1);

namespace Cabbage\Core\Query\Translator\CriterionConverter;

use Cabbage\API\Query\Criterion\CustomField as CustomFieldCriterion;
use Cabbage\Core\Query\Translator\CriterionConverter;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion;

final class CustomField extends CriterionConverter
{
    public function accept(Criterion $criterion): bool
    {
        return $criterion instanceof CustomFieldCriterion;
    }

    public function convert(Criterion $criterion): array
    {
        return [
            'term' => [
                $criterion->target => $criterion->value[0],
            ],
        ];
    }
}
