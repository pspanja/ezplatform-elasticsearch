<?php

declare(strict_types=1);

namespace Cabbage\Core\Query\Translator;

use Cabbage\API\Query\Criterion\DocumentType;

final class CustomFieldCriterionConverter
{
    public function convert(DocumentType $criterion): array
    {
        return [
            'term' => [
                $criterion->target => $criterion->value[0],
            ],
        ];
    }
}
