<?php

declare(strict_types=1);

namespace Cabbage\Core\Document\Serializer;

use Cabbage\SPI\Document;
use Cabbage\SPI\Endpoint;

/**
 * Matches a document to an index.
 */
final class Router
{
    public function match(Document $document): Endpoint
    {
        return Endpoint::fromDsn('http://localhost:9200/index');
    }
}
