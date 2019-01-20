<?php

declare(strict_types=1);

namespace Cabbage\Core\Document;

use Cabbage\SPI\Document;
use Cabbage\SPI\Index;
use Cabbage\SPI\Node;

/**
 * Resolves an index where a document will be indexed.
 */
final class IndexResolver
{
    public function resolve(Document $document): Index
    {
        return new Index(
            Node::fromDsn('http://localhost:9200'),
            'index'
        );
    }
}
