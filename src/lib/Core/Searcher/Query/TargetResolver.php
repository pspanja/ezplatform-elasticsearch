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

    public function __construct(Cluster $cluster)
    {
        $this->cluster = $cluster;
    }

    public function resolve(LanguageFilter $languageFilter): Target
    {
        $index = $this->cluster->getDefaultIndex();

        return new Target($index->node, [$index]);
    }
}
