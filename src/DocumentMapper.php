<?php

declare(strict_types=1);

namespace Cabbage;

/**
 * Document mapper maps nothing to a Document object.
 *
 * @see \Cabbage\Document
 */
final class DocumentMapper
{
    public function map(): Document
    {
        $fields = [
            new Field('test_string', 'value', 'string'),
            new Field('test_bool', true, 'bool'),
        ];

        return new Document('test', $fields);
    }
}
