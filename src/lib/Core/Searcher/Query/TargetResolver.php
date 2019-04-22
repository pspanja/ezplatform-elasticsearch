<?php

declare(strict_types=1);

namespace Cabbage\Core\Searcher\Query;

use Cabbage\Core\IndexRegistry;
use Cabbage\SPI\Index;
use eZ\Publish\API\Repository\Values\Content\Query;

/**
 * Matches a query to a Target.
 *
 * @see \Cabbage\Core\Searcher\Query\Target
 */
final class TargetResolver
{
    /**
     * @var \Cabbage\Core\IndexRegistry
     */
    private $indexRegistry;

    public function __construct(IndexRegistry $indexRegistry)
    {
        $this->indexRegistry = $indexRegistry;
    }

    public function resolve(array $languageFilter): Target
    {
        $index = $this->indexRegistry->get('default');

        return new Target($index->node, [$index]);
    }
}
