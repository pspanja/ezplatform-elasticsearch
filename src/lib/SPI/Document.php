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
     * Identifier of the document's type.
     *
     * @var string
     */
    public $type;

    /**
     * @var string
     */
    public $languageCode;

    /**
     * Document's fields.
     *
     * @var \Cabbage\SPI\Document\Field[]
     */
    public $fields;

    /**
     * @param string $id
     * @param string $type
     * @param string $languageCode
     * @param \Cabbage\SPI\Document\Field[] $fields
     */
    public function __construct(string $id, string $type, string $languageCode, array $fields)
    {
        $this->id = $id;
        $this->type = $type;
        $this->languageCode = $languageCode;
        $this->fields = $fields;
    }
}
