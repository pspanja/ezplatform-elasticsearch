<?php

declare(strict_types=1);

namespace Cabbage\API\Query\Criterion;

use eZ\Publish\API\Repository\Values\Content\Query\Criterion;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\Operator;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\Operator\Specifications;

/**
 * Defines a match on a document field by it's exact name.
 *
 * @see \Cabbage\SPI\Document\Field
 */
final class RawField extends Criterion
{
    /**
     * @param string $field
     * @param mixed $value
     */
    public function __construct(string $field, $value)
    {
        parent::__construct($field, null, $value);
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
