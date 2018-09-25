<?php

declare(strict_types=1);

namespace Cabbage;

use eZ\Publish\API\Repository\Values\Content\LocationQuery;
use eZ\Publish\API\Repository\Values\Content\Query;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion;
use eZ\Publish\API\Repository\Values\Content\Search\SearchResult;
use eZ\Publish\SPI\Persistence\Content;
use eZ\Publish\SPI\Persistence\Content\ContentInfo;
use eZ\Publish\SPI\Persistence\Content\Location;
use eZ\Publish\SPI\Search\Capable;
use eZ\Publish\SPI\Search\Handler as HandlerInterface;

final class Handler implements HandlerInterface, Capable
{
    public function supports($capabilityFlag): bool
    {
        // TODO: Implement supports() method.
    }

    public function findContent(Query $query, array $languageFilter = []): SearchResult
    {
        // TODO: Implement findContent() method.
    }

    public function findSingle(Criterion $filter, array $languageFilter = []): ContentInfo
    {
        // TODO: Implement findSingle() method.
    }

    public function findLocations(LocationQuery $query, array $languageFilter = []): SearchResult
    {
        // TODO: Implement findLocations() method.
    }

    public function suggest($prefix, $fieldPaths = [], $limit = 10, ?Criterion $filter = null): void
    {
        // TODO: Implement suggest() method.
    }

    public function indexContent(Content $content): void
    {
        // TODO: Implement indexContent() method.
    }

    public function deleteContent($contentId, $versionId = null): void
    {
        // TODO: Implement deleteContent() method.
    }

    public function indexLocation(Location $location): void
    {
        // TODO: Implement indexLocation() method.
    }

    public function deleteLocation($locationId, $contentId): void
    {
        // TODO: Implement deleteLocation() method.
    }

    public function purgeIndex(): void
    {
        // TODO: Implement purgeIndex() method.
    }
}
