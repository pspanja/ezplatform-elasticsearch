<?php

declare(strict_types=1);

namespace Cabbage\Core\Searcher\Query;

use Cabbage\Core\Cluster;
use Cabbage\Core\IndexRegistry;
use Cabbage\SPI\LanguageFilter;

/**
 * Matches a LanguageFilter to a Target.
 *
 * @see \Cabbage\Core\Searcher\Query\Target
 * @see \Cabbage\SPI\LanguageFilter
 */
final class TargetResolver
{
    /**
     * @var \Cabbage\Core\Cluster
     */
    private $cluster;

    /**
     * @var \Cabbage\Core\IndexRegistry
     */
    private $indexRegistry;

    public function __construct(Cluster $cluster, IndexRegistry $indexRegistry)
    {
        $this->cluster = $cluster;
        $this->indexRegistry = $indexRegistry;
    }

    public function resolve(LanguageFilter $languageFilter): Target
    {
        $index = $this->indexRegistry->get('default');

        return new Target($index->node, [$index]);
    }
}
