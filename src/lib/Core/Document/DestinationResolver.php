<?php

declare(strict_types=1);

namespace Cabbage\Core\Document;

use Cabbage\Core\IndexRegistry;
use Cabbage\SPI\Document;
use Cabbage\SPI\Index;

/**
 * Resolves an index where a document will be indexed.
 */
final class DestinationResolver
{
    /**
     * @var \Cabbage\Core\IndexRegistry
     */
    private $indexRegistry;

    public function __construct(IndexRegistry $indexRegistry)
    {
        $this->indexRegistry = $indexRegistry;
    }

    public function resolve(Document $document): Index
    {
        return $this->indexRegistry->get('default');
    }
}
