<?php

declare(strict_types=1);

namespace Cabbage\SPI\Document\Field\Type;

use Cabbage\SPI\Document\Field\Type;

/**
 * Represents a identifier field.
 */
final class Identifier extends Type
{
    public function __construct()
    {
        $this->identifier = 'identifier';
    }
}
