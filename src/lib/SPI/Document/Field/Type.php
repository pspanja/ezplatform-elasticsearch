<?php

declare(strict_types=1);

namespace Cabbage\SPI\Document\Field;

use Cabbage\SPI\ValueObject;

/**
 * Represents a type of the field in a Document.
 *
 * @see \Cabbage\SPI\Field
 *
 * @property-read string $identifier Identifier of the field type, must be handled by the Elasticsearch mapping.
 */
abstract class Type extends ValueObject
{
    /**
     * Identifier of the field type, must be handled by the Elasticsearch mapping.
     *
     * @var string
     */
    protected $identifier;
}
