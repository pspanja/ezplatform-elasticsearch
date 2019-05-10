<?php

declare(strict_types=1);

namespace Cabbage\Core;

use Cabbage\Core\Cluster\CoordinatingNodeSelector;
use Cabbage\Core\Cluster\DocumentIndexResolver;
use Cabbage\Core\Searcher\Target;
use Cabbage\Core\Cluster\LanguageFilterTargetResolver;
use Cabbage\SPI\Document;
use Cabbage\SPI\Index;
use Cabbage\SPI\LanguageFilter;
use Cabbage\SPI\Node;

/**
 * Represents Elasticsearch cluster configuration for eZ Platform Repository.
 */
final class Cluster
{
    /**
     * @var \Cabbage\Core\Cluster\DocumentIndexResolver
     */
    private $documentIndexResolver;

    /**
     * @var \Cabbage\Core\Cluster\LanguageFilterTargetResolver
     */
    private $languageFilterTargetResolver;

    /**
     * @var \Cabbage\Core\Cluster\CoordinatingNodeSelector
     */
    private $coordinatingNodeSelector;

    /**
     * @param \Cabbage\Core\Cluster\DocumentIndexResolver $documentIndexResolver
     * @param \Cabbage\Core\Cluster\LanguageFilterTargetResolver $languageFilterTargetResolver
     * @param \Cabbage\Core\Cluster\CoordinatingNodeSelector $coordinatingNodeSelector
     */
    public function __construct(
        LanguageFilterTargetResolver $languageFilterTargetResolver,
        DocumentIndexResolver $documentIndexResolver,
        CoordinatingNodeSelector $coordinatingNodeSelector
    ) {
        $this->documentIndexResolver = $documentIndexResolver;
        $this->languageFilterTargetResolver = $languageFilterTargetResolver;
        $this->coordinatingNodeSelector = $coordinatingNodeSelector;
    }

    public function getIndexForDocument(Document $document): Index
    {
        return $this->documentIndexResolver->resolve($document);
    }

    public function getSearchTargetForLanguageFilter(LanguageFilter $languageFilter): Target
    {
        return $this->languageFilterTargetResolver->resolve($languageFilter);
    }

    public function selectCoordinatingNode(): Node
    {
        return $this->coordinatingNodeSelector->select();
    }
}
