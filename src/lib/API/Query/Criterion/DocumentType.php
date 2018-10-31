<?php

declare(strict_types=1);

namespace Cabbage\API\Query\Criterion;

use eZ\Publish\API\Repository\Values\Content\Query\Criterion;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\Operator;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\Operator\Specifications;

/**
 * Defines a match on document's type.
 *
 * @see \Cabbage\SPI\Document
 */
final class DocumentType extends Criterion
{
    /**
     * @param string $value
     */
    public function __construct(string $value)
    {
        parent::__construct(null, null, $value);
    }

    /**
     * @return \eZ\Publish\API\Repository\Values\Content\Query\Criterion\Operator\Specifications[]
     */
    public function getSpecifications(): array
    {
        return [
            new Specifications(
                Operator::EQ,
                Specifications::FORMAT_SINGLE,
                Specifications::TYPE_STRING
            ),
        ];
    }
}
