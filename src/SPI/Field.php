<?php

declare(strict_types=1);

namespace Cabbage\SPI;

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
     * @var string
     */
    public $type;

    /**
     * @param string $name
     * @param mixed $value
     * @param string $type
     */
    public function __construct(string $name, $value, string $type)
    {
        $this->name = $name;
        $this->value = $value;
        $this->type = $type;
    }
}
