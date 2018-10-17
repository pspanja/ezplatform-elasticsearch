<?php

declare(strict_types=1);

namespace Cabbage\Core\Query\Translator;

use Cabbage\API\Query\Criterion\DocumentType;

final class DocumentTypeCriterionConverter
{
    public function convert(DocumentType $criterion): array
    {
        return [
            'term' => [
                'type' => $criterion->value[0],
            ],
        ];
    }
}
