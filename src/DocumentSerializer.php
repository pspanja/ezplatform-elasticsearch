<?php

declare(strict_types=1);

namespace Cabbage;

use RuntimeException;

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
            $content[$field->name] = $field->value;
        }

        $content = json_encode($content);

        if ($content === false) {
            throw new RuntimeException('Could not JSON encode given document');
        }

        return $content;
    }
}
