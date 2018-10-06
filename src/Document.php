<?php

declare(strict_types=1);

namespace Cabbage;

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
     * @see \Cabbage\Document::TypeContent
     * @see \Cabbage\Document::TypeLocation
     *
     * @var string
     */
    public $type;

    /**
     * Document's fields.
     *
     * @var \Cabbage\Field[]
     */
    public $fields;

    /**
     * @param string $id
     * @param string $type
     * @param \Cabbage\Field[] $fields
     */
    public function __construct(string $id, string $type, array $fields)
    {
        $this->id = $id;
        $this->type = $type;
        $this->fields = $fields;
    }
}
