<?php

declare(strict_types=1);

namespace Cabbage\Core\Query\Translator\Criterion;

use eZ\Publish\API\Repository\Values\Content\Query\Criterion;

/**
 * Criterion visitor translates Criterion object into a fragment of Elasticsearch Query DSL.
 *
 * @see \Cabbage\Core\Query\Translator
 * @see \eZ\Publish\API\Repository\Values\Content\Query\Criterion
 */
abstract class Visitor
{
    /**
     * Check that visitor accepts the criterion.
     *
     * @param \eZ\Publish\API\Repository\Values\Content\Query\Criterion $criterion
     *
     * @return bool
     */
    abstract public function accept(Criterion $criterion): bool;

    /**
     * Visit the criterion using dispatcher for visiting aggregate criteria.
     *
     * @param \eZ\Publish\API\Repository\Values\Content\Query\Criterion $criterion
     * @param \Cabbage\Core\Query\Translator\Criterion\VisitorDispatcher $dispatcher
     *
     * @return array
     */
    abstract public function visit(Criterion $criterion, VisitorDispatcher $dispatcher): array;
}
