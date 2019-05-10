<?php

declare(strict_types=1);

namespace Cabbage\Core;

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
     * @var \Cabbage\SPI\Node[]
     */
    private $coordinatingNodes;

    /**
     * @param \Cabbage\Core\Cluster\TargetResolver $targetResolver
     * @param \Cabbage\Core\Cluster\DestinationResolver $destinationResolver
     * @param \Cabbage\SPI\Node[] $coordinatingNodes
     */
    public function __construct(
        TargetResolver $targetResolver,
        DestinationResolver $destinationResolver,
        array $coordinatingNodes
    ) {
        $this->targetResolver = $targetResolver;
        $this->destinationResolver = $destinationResolver;
        $this->coordinatingNodes = $coordinatingNodes;
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
        if (empty($this->coordinatingNodes)) {
            throw new RuntimeException(
                'No coordinating Nodes are defined'
            );
        }

        return $this->coordinatingNodes[array_rand($this->coordinatingNodes, 1)];
    }
}
