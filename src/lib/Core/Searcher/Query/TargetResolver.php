<?php

declare(strict_types=1);

namespace Cabbage\Core\Searcher\Query;

use Cabbage\Core\IndexRegistry;
use Cabbage\SPI\Index;
use eZ\Publish\API\Repository\Values\Content\Query;

/**
 * Matches a query to an Index.
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

    public function resolve(Query $query): Index
    {
        return $this->indexRegistry->get('default');
    }
}
