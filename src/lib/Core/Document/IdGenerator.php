<?php

declare(strict_types=1);

namespace Cabbage\Core\Document;

use eZ\Publish\SPI\Persistence\Content;
use eZ\Publish\SPI\Persistence\Content\Location;

/**
 * Generates Content and Location document IDs.
 */
final class IdGenerator
{
    /**
     * @param \eZ\Publish\SPI\Persistence\Content $content
     *
     * @return string
     */
    public function generateContentDocumentId(Content $content): string
    {
        return "content_{$content->versionInfo->contentInfo->id}";
    }

    /**
     * @param \eZ\Publish\SPI\Persistence\Content\Location $location
     *
     * @return string
     */
    public function generateLocationDocumentId(Location $location): string
    {
        return "location_{$location->id}";
    }
}
