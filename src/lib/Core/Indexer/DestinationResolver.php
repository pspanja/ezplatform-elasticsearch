<?php

declare(strict_types=1);

namespace Cabbage\Core\Indexer;

use Cabbage\Core\Cluster;
use Cabbage\SPI\Document;
use Cabbage\SPI\Index;
use RuntimeException;

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
        if ($this->cluster->hasIndexForLanguage($document->languageCode)) {
            return $this->cluster->getIndexForLanguage($document->languageCode);
        }

        if ($this->cluster->hasDefaultIndex()) {
            return $this->cluster->getDefaultIndex();
        }

        throw new RuntimeException(
            "Couldn't resolve index for Document with language code '{$document->languageCode}'"
        );
    }
}
