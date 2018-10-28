<?php

declare(strict_types=1);

namespace Cabbage\SPI;

/**
 * Represents a type of the field in a Document.
 *
 * @see \Cabbage\SPI\Field
 *
 * @property-read string $identifier Identifier of the field type, must be handled by the Elasticsearch mapping.
 */
abstract class FieldType extends ValueObject
{
    /**
     * Identifier of the field type, must be handled by the Elasticsearch mapping.
     *
     * @var string
     */
    protected $identifier;
}
