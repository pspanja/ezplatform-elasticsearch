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
     * @var string
     */
    public $name;

    /**
     * @var mixed
     */
    public $value;

    /**
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
