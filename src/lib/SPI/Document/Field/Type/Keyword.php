<?php

declare(strict_types=1);

namespace Cabbage\SPI\Document\Field\Type;

use Cabbage\SPI\Document\Field\Type;

/**
 * Represents a keyword string field.
 *
 * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/keyword.html
 */
final class Keyword extends Type
{
    public function __construct()
    {
        $this->identifier = 'keyword';
    }
}
