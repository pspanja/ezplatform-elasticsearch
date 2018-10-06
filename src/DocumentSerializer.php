<?php

declare(strict_types=1);

namespace Cabbage;

use RuntimeException;

/**
 * Serializes a Document object into a JSON string for indexing.
 *
 * @see \Cabbage\Document
 */
final class DocumentSerializer
{
    /**
     * @param \Cabbage\Document $document
     *
     * @return string
     */
    public function serialize(Document $document): string
    {
        $content = [
            'type' => $document->type,
        ];

        foreach ($document->fields as $field) {
            $content[$this->mapFieldName($field)] = $this->mapValue($field);
        }

        $content = json_encode($content);

        if ($content === false) {
            throw new RuntimeException('Could not JSON encode given document');
        }

        return $content;
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
