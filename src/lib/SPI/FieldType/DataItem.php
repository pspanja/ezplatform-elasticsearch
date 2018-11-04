<?php

declare(strict_types=1);

namespace Cabbage\SPI\FieldType;

use Cabbage\SPI\Document\Field\Type;

/**
 * Represents a Content Field data for indexing.
 *
 * @see \eZ\Publish\SPI\Persistence\Content\Field
 */
final class DataItem
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
