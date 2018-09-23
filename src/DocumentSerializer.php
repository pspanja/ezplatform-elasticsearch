<?php

declare(strict_types=1);

namespace Cabbage;

use RuntimeException;

/**
 * Document serializer serializes a Document object into a JSON string for indexing.
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
        $content = [];

        foreach ($document->fields as $field) {
            $content[$field->name] = $this->mapValue($field);
        }

        $content = json_encode($content);

        if ($content === false) {
            throw new RuntimeException('Could not JSON encode given document');
        }

        return $content;
    }

    private function mapValue(Field $field): string
    {
        return $field->value;
    }
}
