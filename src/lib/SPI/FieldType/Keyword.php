<?php

declare(strict_types=1);

namespace Cabbage\SPI\FieldType;

use Cabbage\SPI\FieldType;

/**
 * Represents a keyword string field.
 *
 * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/keyword.html
 */
final class Keyword extends FieldType
{
    public function __construct()
    {
        $this->identifier = 'keyword';
    }
}
