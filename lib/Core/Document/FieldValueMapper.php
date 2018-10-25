<?php

declare(strict_types=1);

namespace Cabbage\Core\Document;

use Cabbage\SPI\Field;
use RuntimeException;

/**
 * Maps field's value to a search engine format.
 *
 * @see \Cabbage\SPI\Field
 */
final class FieldValueMapper
{
    public function map(Field $field)
    {
        switch ($field->type) {
            case 'string':
                return (string)$field->value;
            case 'bool':
                return (bool)$field->value;
        }

        throw new RuntimeException(
            "Field of type '{$field->type}' is not handled"
        );
    }
}
