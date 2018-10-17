<?php

declare(strict_types=1);

namespace Cabbage\Core\Query\Translator;

use Cabbage\API\Query\Criterion\CustomField;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion;
use RuntimeException;

final class CustomFieldCriterionConverter extends CriterionConverter
{
    public function convert(Criterion $criterion): array
    {
        if (!$criterion instanceof CustomField) {
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
