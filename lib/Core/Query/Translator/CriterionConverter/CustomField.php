<?php

declare(strict_types=1);

namespace Cabbage\Core\Query\Translator\CriterionConverter;

use Cabbage\API\Query\Criterion\CustomField as CustomFieldCriterion;
use Cabbage\Core\Query\Translator\CriterionConverter;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion;
use RuntimeException;

final class CustomField extends CriterionConverter
{
    public function convert(Criterion $criterion): array
    {
        if (!$criterion instanceof CustomFieldCriterion) {
            throw new RuntimeException(
                'This converter does not accept the given criterion'
            );
        }

        return [
            'term' => [
                $criterion->target => $criterion->value[0],
            ],
        ];
    }
}
