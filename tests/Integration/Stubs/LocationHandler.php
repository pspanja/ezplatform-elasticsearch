<?php

namespace Cabbage\Tests\Integration\Stubs;

use eZ\Publish\SPI\Persistence\Content\Location;
use eZ\Publish\SPI\Persistence\Content\Location\CreateStruct;
use eZ\Publish\SPI\Persistence\Content\Location\Handler as LocationHandlerInterface;
use eZ\Publish\SPI\Persistence\Content\Location\UpdateStruct;
use RuntimeException;

class LocationHandler implements LocationHandlerInterface
{
    public function load($locationId)
    {
        throw new RuntimeException('Not implemented');
    }

    public function loadSubtreeIds($locationId)
    {
        throw new RuntimeException('Not implemented');
    }

    public function loadByRemoteId($remoteId)
    {
        throw new RuntimeException('Not implemented');
    }

    public function loadLocationsByContent($contentId, $rootLocationId = null): array
    {
        return [
            new Location([]),
        ];
    }

    public function loadParentLocationsForDraftContent($contentId)
    {
        throw new RuntimeException('Not implemented');
    }

    public function copySubtree($sourceId, $destinationParentId)
    {
        throw new RuntimeException('Not implemented');
    }

    public function move($sourceId, $destinationParentId)
    {
        throw new RuntimeException('Not implemented');
    }

    public function markSubtreeModified($locationId, $timestamp = null)
    {
        throw new RuntimeException('Not implemented');
    }

    public function hide($id): void
    {
        throw new RuntimeException('Not implemented');
    }

    public function unHide($id): void
    {
        throw new RuntimeException('Not implemented');
    }

    public function swap($locationId1, $locationId2)
    {
        throw new RuntimeException('Not implemented');
    }

    public function update(UpdateStruct $location, $locationId): void
    {
        throw new RuntimeException('Not implemented');
    }

    public function create(CreateStruct $location)
    {
        throw new RuntimeException('Not implemented');
    }

    public function removeSubtree($locationId)
    {
        throw new RuntimeException('Not implemented');
    }

    public function setSectionForSubtree($locationId, $sectionId): void
    {
        throw new RuntimeException('Not implemented');
    }

    public function changeMainLocation($contentId, $locationId): void
    {
        throw new RuntimeException('Not implemented');
    }

    public function countAllLocations()
    {
        throw new RuntimeException('Not implemented');
    }

    public function loadAllLocations($offset, $limit)
    {
        throw new RuntimeException('Not implemented');
    }
}
