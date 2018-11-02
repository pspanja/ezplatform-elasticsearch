<?php

declare(strict_types=1);

namespace Cabbage\SPI\Document\Field\Type;

use Cabbage\SPI\Document\Field\Type;

/**
 * Represents a boolean field.
 *
 * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/boolean.html
 */
final class Boolean extends Type
{
    public function __construct()
    {
        $this->identifier = 'boolean';
    }
}
