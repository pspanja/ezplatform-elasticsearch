<?php

declare(strict_types=1);

namespace Cabbage\Core\Query\Translator;

use eZ\Publish\API\Repository\Values\Content\Query\Criterion;

abstract class CriterionVisitor
{
    /**
     * @param \eZ\Publish\API\Repository\Values\Content\Query\Criterion $criterion
     *
     * @return bool
     */
    abstract public function accept(Criterion $criterion): bool;

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\Query\Criterion $criterion
     * @param \Cabbage\Core\Query\Translator\CriterionVisitor $subVisitor
     *
     * @return array
     */
    abstract public function visit(
        Criterion $criterion,
        CriterionVisitor $subVisitor = null
    ): array;
}
