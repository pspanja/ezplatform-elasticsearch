<?php

declare(strict_types=1);

namespace Cabbage\SPI;

/**
 * Represents a document to be indexed in the Elasticsearch backed.
 */
final class Document
{
    /**
     * Unique ID of the Document.
     *
     * @var string
     */
    public $id;

    /**
     * Document's fields.
     *
     * @var \Cabbage\SPI\Document\Field[]
     */
    public $fields;

    /**
     * @param string $id
     * @param \Cabbage\SPI\Document\Field[] $fields
     */
    public function __construct(string $id, array $fields)
    {
        $this->id = $id;
        $this->fields = $fields;
    }
}
