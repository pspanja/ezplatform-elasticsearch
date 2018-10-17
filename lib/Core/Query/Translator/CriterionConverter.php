<?php

declare(strict_types=1);

namespace Cabbage\Core\Query\Translator;

use eZ\Publish\API\Repository\Values\Content\Query\Criterion;

abstract class CriterionConverter
{
    /**
     * @param \eZ\Publish\API\Repository\Values\Content\Query\Criterion $criterion
     *
     * @return bool
     */
    abstract public function accept(Criterion $criterion): bool;

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\Query\Criterion $criterion
     *
     * @return array
     */
    abstract public function convert(Criterion $criterion): array;
}
