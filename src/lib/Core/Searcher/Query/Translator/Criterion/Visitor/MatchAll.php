<?php

declare(strict_types=1);

namespace Cabbage\Core\Searcher\Query\Translator\Criterion\Visitor;

use ArrayObject;
use Cabbage\Core\Searcher\Query\Translator\Criterion\Visitor;
use Cabbage\Core\Searcher\Query\Translator\Criterion\Converter;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\MatchAll as MatchAllCriterion;

/**
 * Visits MatchAll criterion.
 *
 * @see \eZ\Publish\API\Repository\Values\Content\Query\Criterion\MatchAll
 */
final class MatchAll extends Visitor
{
    public function accept(Criterion $criterion): bool
    {
        return $criterion instanceof MatchAllCriterion;
    }

    public function visit(Criterion $criterion, Converter $converter): array
    {
        return [
            'match_all' => new ArrayObject(),
        ];
    }
}
