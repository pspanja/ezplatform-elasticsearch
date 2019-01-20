<?php

declare(strict_types=1);

namespace Cabbage\Core\Query;

use Cabbage\SPI\Index;
use Cabbage\SPI\Node;
use eZ\Publish\API\Repository\Values\Content\Query;

/**
 * Matches a query to an Index.
 */
final class TargetResolver
{
    public function resolve(Query $query): Index
    {
        return new Index(
            Node::fromDsn('http://localhost:9200'),
            'index'
        );
    }
}
