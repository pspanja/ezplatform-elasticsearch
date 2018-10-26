<?php

declare(strict_types=1);

namespace Cabbage\Core\Document\Field;

use Cabbage\SPI\Field;
use Cabbage\SPI\FieldType\Boolean;
use Cabbage\SPI\FieldType\Keyword;
use RuntimeException;

/**
 * Maps field's value to a search engine format.
 *
 * @see \Cabbage\SPI\Field
 */
final class ValueMapper
{
    public function map(Field $field)
    {
        if ($field->type instanceof Keyword) {
            return (string)$field->value;
        }

        if ($field->type instanceof Boolean) {
            return (bool)$field->value;
        }

        $type = \get_class($field->type);

        throw new RuntimeException(
            "Field of type '{$type}' is not handled"
        );
    }
}
