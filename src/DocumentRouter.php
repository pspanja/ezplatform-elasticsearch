<?php

declare(strict_types=1);

namespace Cabbage;

/**
 * Matches a document to an index.
 */
class DocumentRouter
{
    public function match(Document $document): Endpoint
    {
        return Endpoint::fromDsn('http://localhost:9200/index');
    }
}
