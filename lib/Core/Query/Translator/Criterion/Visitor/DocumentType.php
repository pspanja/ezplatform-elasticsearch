<?php

declare(strict_types=1);

namespace Cabbage\Core\Query\Translator\Criterion\Visitor;

use Cabbage\API\Query\Criterion\DocumentType as DocumentTypeCriterion;
use Cabbage\Core\Query\Translator\Criterion\Visitor;
use Cabbage\Core\Query\Translator\Criterion\VisitorDispatcher;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion;

final class DocumentType extends Visitor
{
    public function accept(Criterion $criterion): bool
    {
        return $criterion instanceof DocumentTypeCriterion;
    }

    public function visit(Criterion $criterion, VisitorDispatcher $dispatcher): array
    {
        return [
            'term' => [
                'type' => $criterion->value[0],
            ],
        ];
    }
}
