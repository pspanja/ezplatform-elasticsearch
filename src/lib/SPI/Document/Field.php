<?php

declare(strict_types=1);

namespace Cabbage\SPI\Document;

use Cabbage\SPI\Document\Field\Type;

/**
 * Represents a field in a Document.
 *
 * @see \Cabbage\SPI\Document
 */
final class Field
{
    /**
     * Name of the field.
     *
     * Used to generate name of the field in the Elasticsearch index.
     *
     * @var string
     */
    public $name;

    /**
     * Value of the field.
     *
     * The value can be of any type, it will be mapped to a value in the
     * Elasticsearch index using the type instance.
     *
     * @see \Cabbage\SPI\Document\Field::$type
     *
     * @var mixed
     */
    public $value;

    /**
     * Type of the field's value.
     *
     * @var \Cabbage\SPI\Document\Field\Type
     */
    public $type;

    /**
     * @param string $name
     * @param mixed $value
     * @param \Cabbage\SPI\Document\Field\Type $type
     */
    public function __construct(string $name, $value, Type $type)
    {
        $this->name = $name;
        $this->value = $value;
        $this->type = $type;
    }
}
