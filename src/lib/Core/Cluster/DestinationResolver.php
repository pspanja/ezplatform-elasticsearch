<?php

declare(strict_types=1);

namespace Cabbage\Core\Cluster;

use Cabbage\Core\Cluster;
use Cabbage\SPI\Document;
use Cabbage\SPI\Index;
use RuntimeException;

/**
 * Resolves an index where a document will be indexed.
 */
final class DestinationResolver
{
    public function resolve(Cluster $cluster, Document $document): Index
    {
        if ($cluster->hasIndexForLanguage($document->languageCode)) {
            return $cluster->getIndexForLanguage($document->languageCode);
        }

        if ($cluster->hasDefaultIndex()) {
            return $cluster->getDefaultIndex();
        }

        throw new RuntimeException(
            "Couldn't resolve index for Document with language code '{$document->languageCode}'"
        );
    }
}
