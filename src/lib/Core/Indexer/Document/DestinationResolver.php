<?php

declare(strict_types=1);

namespace Cabbage\Core\Indexer\Document;

use Cabbage\Core\Cluster;
use Cabbage\SPI\Document;
use Cabbage\SPI\Index;

/**
 * Resolves an index where a document will be indexed.
 */
final class DestinationResolver
{
    /**
     * @var \Cabbage\Core\Cluster
     */
    private $cluster;

    public function __construct(Cluster $cluster)
    {
        $this->cluster = $cluster;
    }

    public function resolve(Document $document): Index
    {
        return $this->cluster->getDefaultIndex();
    }
}
