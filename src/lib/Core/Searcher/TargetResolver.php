<?php

declare(strict_types=1);

namespace Cabbage\Core\Searcher;

use Cabbage\Core\Cluster;
use Cabbage\SPI\LanguageFilter;

/**
 * Matches a LanguageFilter to a Target.
 *
 * @see \Cabbage\Core\Searcher\Target
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
