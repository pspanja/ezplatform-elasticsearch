<?php

declare(strict_types=1);

namespace Cabbage;

/**
 * Maps nothing to a Document object.
 *
 * @see \Cabbage\Document
 */
final class DocumentMapper
{
    /**
     * @return \Cabbage\Document[]
     */
    public function map(): array
    {
        $fields = [
            new Field('test_string', 'value', 'string'),
            new Field('test_bool', true, 'bool'),
        ];

        return [
            new Document(uniqid('blah', true), 'content', $fields),
            new Document(uniqid('blah', true), 'location', $fields),
        ];
    }
}
