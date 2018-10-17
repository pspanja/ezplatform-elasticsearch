<?php

declare(strict_types=1);

namespace Cabbage\Core\Query\Translator;

use Cabbage\API\Query\Criterion\CustomField;

final class CustomFieldCriterionConverter
{
    public function convert(CustomField $criterion): array
    {
        return [
            'term' => [
                $criterion->target => $criterion->value[0],
            ],
        ];
    }
}
