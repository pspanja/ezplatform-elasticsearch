<?php

declare(strict_types=1);

namespace Cabbage\Core\Document\BulkSerializer\FieldSerializer\ValueMapper;

use Cabbage\SPI\Document\Field;

/**
 * Maps field's value to search engine format.
 *
 * @see \Cabbage\SPI\Field
 */
abstract class Visitor
{
    /**
     * Check that visitor can map the field's value.
     *
     * @param \Cabbage\SPI\Document\Field $field
     *
     * @return bool
     */
    abstract public function accept(Field $field): bool;

    /**
     * Map the field's value.
     *
     * @param \Cabbage\SPI\Document\Field $field
     *
     * @return mixed
     */
    abstract public function visit(Field $field);
}
