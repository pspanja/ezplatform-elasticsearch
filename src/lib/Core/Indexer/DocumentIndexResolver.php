<?php

declare(strict_types=1);

namespace Cabbage\Core\Indexer;

use Cabbage\Core\Cluster\Configuration;
use Cabbage\SPI\Document;
use Cabbage\SPI\Index;
use RuntimeException;

/**
 * Resolves an index where a document will be indexed.
 */
final class DocumentIndexResolver
{
    /**
     * @var \Cabbage\Core\Cluster\Configuration
     */
    private $configuration;

    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;
    }

    public function resolve(Document $document): Index
    {
        if ($this->configuration->hasIndexForLanguage($document->languageCode)) {
            return $this->configuration->getIndexForLanguage($document->languageCode);
        }

        if ($this->configuration->hasDefaultIndex()) {
            return $this->configuration->getDefaultIndex();
        }

        throw new RuntimeException(
            "No index is configured for Document with language code '{$document->languageCode}'"
        );
    }
}
