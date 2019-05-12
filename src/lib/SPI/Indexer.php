<?php

declare(strict_types=1);

namespace Cabbage\SPI;

abstract class Indexer
{
    /**
     * @see \eZ\Publish\SPI\Search\Handler::deleteContent()
     *
     * @param int $contentId
     * @param int|null $versionId
     */
    abstract public function deleteContent($contentId, $versionId = null): void;

    /**
     * @see \eZ\Publish\SPI\Search\Handler::deleteLocation()
     *
     * @param mixed $locationId
     * @param mixed $contentId
     */
    abstract public function deleteLocation($locationId, $contentId): void;

    /**
     * @see \eZ\Publish\SPI\Search\Handler::purgeIndex()
     */
    abstract public function purgeIndex(): void;

    /**
     * @param \eZ\Publish\SPI\Persistence\Content[] $contentItems
     */
    abstract public function bulkIndexContent(array $contentItems): void;

    abstract public function flush(): void;

    abstract public function refresh(): void;
}
