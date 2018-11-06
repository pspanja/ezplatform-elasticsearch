<?php

declare(strict_types=1);

namespace Cabbage\SPI;

/**
 * Represents a document to be indexed in the Elasticsearch backed.
 */
final class Document
{
    /**
     * Content document type identifier.
     *
     * @var string
     */
    public const TypeContent = 'content';

    /**
     * Location document type identifier.
     *
     * @var string
     */
    public const TypeLocation = 'location';

    /**
     * @var string
     */
    public $id;

    /**
     * Document type identifier.
     *
     * @see \Cabbage\SPI\Document::TypeContent
     * @see \Cabbage\SPI\Document::TypeLocation
     *
     * @var string
     */
    public $type;

    /**
     * Document's fields.
     *
     * @var \Cabbage\SPI\Document\Field[]
     */
    public $fields;

    /**
     * @param string $id
     * @param string $type
     * @param \Cabbage\SPI\Document\Field[] $fields
     */
    public function __construct(string $id, string $type, array $fields)
    {
        $this->id = $id;
        $this->type = $type;
        $this->fields = $fields;
    }
}
