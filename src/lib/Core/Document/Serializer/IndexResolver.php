<?php

declare(strict_types=1);

namespace Cabbage\Core\Document\Serializer;

use Cabbage\SPI\Document;
use Cabbage\SPI\Endpoint;

/**
 * Resolves an index where a document will be indexed.
 *
 * @see \Cabbage\Core\Document\Serializer
 */
final class IndexResolver
{
    public function resolve(Document $document): Endpoint
    {
        return Endpoint::fromDsn('http://localhost:9200/index');
    }
}