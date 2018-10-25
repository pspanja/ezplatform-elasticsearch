<?php

declare(strict_types=1);

namespace Cabbage\Core\Document;

use Cabbage\SPI\Document;
use Cabbage\SPI\Field;
use RuntimeException;

/**
 * Serializes a Document object into a JSON string for indexing.
 *
 * @see \Cabbage\SPI\Document
 */
final class Serializer
{
    /**
     * @var \Cabbage\Core\Document\FieldValueMapper
     */
    private $fieldValueMapper;

    /**
     * @param \Cabbage\Core\Document\FieldValueMapper $fieldValueMapper
     */
    public function __construct(FieldValueMapper $fieldValueMapper)
    {
        $this->fieldValueMapper = $fieldValueMapper;
    }

    /**
     * @param \Cabbage\SPI\Document $document
     *
     * @return string
     */
    public function serialize(Document $document): string
    {
        $data = [
            'type' => $document->type,
        ];

        foreach ($document->fields as $field) {
            $data[$this->generateFieldName($field)] = $this->fieldValueMapper->map($field);
        }

        $data = json_encode($data);

        if ($data === false) {
            throw new RuntimeException('Could not JSON encode given document');
        }

        return $data;
    }

    private function generateFieldName(Field $field): string
    {
        return $field->name;
    }
}
