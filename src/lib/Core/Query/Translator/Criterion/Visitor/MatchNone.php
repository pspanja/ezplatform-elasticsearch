<?php

declare(strict_types=1);

namespace Cabbage\Core\Query\Translator\Criterion\Visitor;

use ArrayObject;
use Cabbage\Core\Query\Translator\Criterion\Visitor;
use Cabbage\Core\Query\Translator\Criterion\Converter;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\MatchNone as MatchNoneCriterion;

/**
 * Visits MatchNone criterion.
 *
 * @see \eZ\Publish\API\Repository\Values\Content\Query\Criterion\MatchNone
 */
final class MatchNone extends Visitor
{
    public function accept(Criterion $criterion): bool
    {
        return $criterion instanceof MatchNoneCriterion;
    }

    public function visit(Criterion $criterion, Converter $converter): array
    {
        return ['match_none' => new ArrayObject()];
    }
}
