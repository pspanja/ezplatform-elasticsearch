<?php

declare(strict_types=1);

namespace Cabbage\Core\Query\Translator\Criterion;

use eZ\Publish\API\Repository\Values\Content\Query\Criterion;

abstract class Visitor
{
    /**
     * @param \eZ\Publish\API\Repository\Values\Content\Query\Criterion $criterion
     *
     * @return bool
     */
    abstract public function accept(Criterion $criterion): bool;

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\Query\Criterion $criterion
     * @param \Cabbage\Core\Query\Translator\Criterion\VisitorDispatcher $dispatcher
     *
     * @return array
     */
    abstract public function visit(Criterion $criterion, VisitorDispatcher $dispatcher): array;
}
