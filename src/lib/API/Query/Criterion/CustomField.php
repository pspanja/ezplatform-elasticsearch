<?php

declare(strict_types=1);

namespace Cabbage\API\Query\Criterion;

use eZ\Publish\API\Repository\Values\Content\Query\Criterion;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\Operator;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\Operator\Specifications;

/**
 * Defines a match on a custom field.
 *
 * @see \Cabbage\SPI\Document
 */
final class CustomField extends Criterion
{
    /**
     * @param string $field
     * @param mixed $value
     */
    public function __construct(string $field, $value)
    {
        parent::__construct($field, null, $value);
    }

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
