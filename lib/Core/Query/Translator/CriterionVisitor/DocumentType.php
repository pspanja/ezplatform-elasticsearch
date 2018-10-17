<?php

declare(strict_types=1);

namespace Cabbage\Core\Query\Translator\CriterionVisitor;

use Cabbage\API\Query\Criterion\DocumentType as DocumentTypeCriterion;
use Cabbage\Core\Query\Translator\CriterionVisitor;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion;

final class DocumentType extends CriterionVisitor
{
    public function accept(Criterion $criterion): bool
    {
        return $criterion instanceof DocumentTypeCriterion;
    }

    public function visit(Criterion $criterion): array
    {
        return [
            'term' => [
                'type' => $criterion->value[0],
            ],
        ];
    }
}
