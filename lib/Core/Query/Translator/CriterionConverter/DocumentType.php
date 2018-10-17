<?php

declare(strict_types=1);

namespace Cabbage\Core\Query\Translator\CriterionConverter;

use Cabbage\API\Query\Criterion\DocumentType as DocumentTypeCriterion;
use Cabbage\Core\Query\Translator\CriterionConverter;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion;
use RuntimeException;

final class DocumentType extends CriterionConverter
{
    public function convert(Criterion $criterion): array
    {
        if (!$criterion instanceof DocumentTypeCriterion) {
            throw new RuntimeException(
                'This converter does not accept the given criterion'
            );
        }

        return [
            'term' => [
                'type' => $criterion->value[0],
            ],
        ];
    }
}
