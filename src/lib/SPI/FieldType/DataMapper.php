<?php

declare(strict_types=1);

namespace Cabbage\SPI\FieldType;

use eZ\Publish\SPI\Persistence\Content\Field;
use eZ\Publish\SPI\Persistence\Content\Type\FieldDefinition;

/**
 * Maps Content Field data to an array of search DataItem instances.
 *
 * Needs to be implemented per FieldType.
 *
 * @see \eZ\Publish\SPI\Persistence\Content\Field $field
 * @see \Cabbage\SPI\FieldType\DataItem
 */
abstract class DataMapper
{
    /**
     * @param \eZ\Publish\SPI\Persistence\Content\Field $field
     * @param \eZ\Publish\SPI\Persistence\Content\Type\FieldDefinition $fieldDefinition
     *
     * @return \Cabbage\SPI\FieldType\DataItem[]
     */
    abstract public function map(Field $field, FieldDefinition $fieldDefinition): array;
}
