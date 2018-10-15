<?php

declare(strict_types=1);

namespace Cabbage\API\Query\Translator\Criterion;

use eZ\Publish\API\Repository\Values\Content\Query\Criterion;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\Operator;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\Operator\Specifications;

/**
 * Defines a match on document's type.
 *
 * @see \Cabbage\Document
 */
final class DocumentType extends Criterion
{
    public function getSpecifications(): array
    {
        return array(
            new Specifications(
                Operator::EQ,
                Specifications::FORMAT_SINGLE,
                Specifications::TYPE_STRING
            ),
        );
    }
}
