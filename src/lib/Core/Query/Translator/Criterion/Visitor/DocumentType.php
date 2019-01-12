<?php

declare(strict_types=1);

namespace Cabbage\Core\Query\Translator\Criterion\Visitor;

use Cabbage\API\Query\Criterion\DocumentType as DocumentTypeCriterion;
use Cabbage\Core\Query\Translator\Criterion\Visitor;
use Cabbage\Core\Query\Translator\Criterion\Converter;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion;

/**
 * Visits DocumentType criterion.
 *
 * @see \Cabbage\API\Query\Criterion\DocumentType
 */
final class DocumentType extends Visitor
{
    public function accept(Criterion $criterion): bool
    {
        return $criterion instanceof DocumentTypeCriterion;
    }

    public function visit(Criterion $criterion, Converter $converter): array
    {
        return [
            'term' => [
                'type_identifier' => $criterion->value[0],
            ],
        ];
    }
}
