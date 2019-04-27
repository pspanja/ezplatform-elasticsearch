<?php

declare(strict_types=1);

namespace Cabbage\Core\Searcher\QueryTranslator\Criterion;

use eZ\Publish\API\Repository\Values\Content\Query\Criterion;

/**
 * Criterion visitor translates Criterion object into a fragment of Elasticsearch Query DSL.
 *
 * It uses criterion converter to translate aggregated criteria like logical operators.
 *
 * @see \Cabbage\Core\Searcher\QueryTranslator
 * @see \Cabbage\Core\Searcher\QueryTranslator\Criterion\Converter
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
     * Visit the criterion using converter for visiting aggregate criteria.
     *
     * @param \eZ\Publish\API\Repository\Values\Content\Query\Criterion $criterion
     * @param \Cabbage\Core\Searcher\QueryTranslator\Criterion\Converter $converter
     *
     * @return array
     */
    abstract public function visit(Criterion $criterion, Converter $converter): array;
}
