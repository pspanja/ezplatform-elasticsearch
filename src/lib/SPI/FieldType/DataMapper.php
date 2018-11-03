<?php

declare(strict_types=1);

namespace Cabbage\SPI\FieldType;

use eZ\Publish\SPI\Persistence\Content\Field;
use eZ\Publish\SPI\Persistence\Content\Type\FieldDefinition;

/**
 * Maps Content Field data to an array of search Fields.
 *
 * Needs to be implemented per FieldType.
 *
 * @see \eZ\Publish\SPI\Persistence\Content\Field $field
 * @see \eZ\Publish\SPI\Search\Field
 */
abstract class DataMapper
{
    /**
     * @param \eZ\Publish\SPI\Persistence\Content\Field $field
     * @param \eZ\Publish\SPI\Persistence\Content\Type\FieldDefinition $fieldDefinition
     *
     * @return \Cabbage\SPI\Document\Field[]
     */
    abstract public function map(Field $field, FieldDefinition $fieldDefinition): array;
}
