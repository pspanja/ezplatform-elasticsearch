<?php

declare(strict_types=1);

namespace Cabbage\Core;

use Cabbage\Core\Cluster\CoordinatingNodeSelector;
use Cabbage\Core\Cluster\DestinationResolver;
use Cabbage\Core\Searcher\Target;
use Cabbage\Core\Cluster\TargetResolver;
use Cabbage\SPI\Document;
use Cabbage\SPI\Index;
use Cabbage\SPI\LanguageFilter;
use Cabbage\SPI\Node;
use RuntimeException;

/**
 * Represents Elasticsearch cluster configuration for eZ Platform Repository.
 */
final class Cluster
{
    /**
     * @var \Cabbage\Core\Cluster\TargetResolver
     */
    private $targetResolver;

    /**
     * @var \Cabbage\Core\Cluster\DestinationResolver
     */
    private $destinationResolver;

    /**
     * @var \Cabbage\Core\Cluster\CoordinatingNodeSelector
     */
    private $coordinatingNodeSelector;

    /**
     * @param \Cabbage\Core\Cluster\TargetResolver $targetResolver
     * @param \Cabbage\Core\Cluster\DestinationResolver $destinationResolver
     * @param \Cabbage\Core\Cluster\CoordinatingNodeSelector $coordinatingNodeSelector
     */
    public function __construct(
        TargetResolver $targetResolver,
        DestinationResolver $destinationResolver,
        CoordinatingNodeSelector $coordinatingNodeSelector
    ) {
        $this->targetResolver = $targetResolver;
        $this->destinationResolver = $destinationResolver;
        $this->coordinatingNodeSelector = $coordinatingNodeSelector;
    }

    public function getIndexForDocument(Document $document): Index
    {
        return $this->destinationResolver->resolve($document);
    }

    public function getSearchTargetForLanguageFilter(LanguageFilter $languageFilter): Target
    {
        return $this->targetResolver->resolve($languageFilter);
    }

    public function selectCoordinatingNode(): Node
    {
        return $this->coordinatingNodeSelector->select();
    }
}
