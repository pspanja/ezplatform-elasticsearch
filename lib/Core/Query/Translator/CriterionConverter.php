<?php

declare(strict_types=1);

namespace Cabbage\Core\Query\Translator;

use eZ\Publish\API\Repository\Values\Content\Query\Criterion;

abstract class CriterionConverter
{
    abstract public function convert(Criterion $criterion): array;
}
