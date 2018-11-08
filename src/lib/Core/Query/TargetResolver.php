<?php

declare(strict_types=1);

namespace Cabbage\Core\Query;

use Cabbage\SPI\Endpoint;
use eZ\Publish\API\Repository\Values\Content\Query;

/**
 * Matches a query to an index.
 */
final class TargetResolver
{
    public function resolve(Query $query): Endpoint
    {
        return Endpoint::fromDsn('http://localhost:9200/index');
    }
}
