<?php

declare(strict_types=1);

namespace Cabbage;

use Cabbage\SPI\Document;
use RuntimeException;

/**
 * Serializes a Document object into a JSON string for indexing.
 *
 * @see \Cabbage\SPI\Document
 */
final class DocumentSerializer
{
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
            $data[$this->mapFieldName($field)] = $this->mapValue($field);
        }

        $data = json_encode($data);

        if ($data === false) {
            throw new RuntimeException('Could not JSON encode given document');
        }

        return $data;
    }

    /**
     * @param \Cabbage\Field $field
     *
     * @return bool|string
     */
    private function mapValue(Field $field)
    {
        switch ($field->type) {
            case 'string':
                return (string)$field->value;
            case 'bool':
                return (bool)$field->value;
        }

        throw new RuntimeException("Field of type '{$field->type}' is not handled");
    }

    private function mapFieldName(Field $field): string
    {
        return $field->name;
    }
}
