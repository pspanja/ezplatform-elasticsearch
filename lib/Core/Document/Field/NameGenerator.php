<?php

declare(strict_types=1);

namespace Cabbage\Core\Document\Field;

use Cabbage\SPI\Field;

/**
 * Generates the name of the field in the search engine.
 *
 * @see \Cabbage\SPI\Field
 */
final class NameGenerator
{
    public function generate(Field $field): string
    {
        return $field->name;
    }
}
