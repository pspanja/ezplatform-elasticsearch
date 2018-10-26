<?php

declare(strict_types=1);

namespace Cabbage\Core\Document;

use Cabbage\SPI\Document;
use Cabbage\SPI\Field;
use Cabbage\SPI\FieldType\Boolean;
use Cabbage\SPI\FieldType\Keyword;
use eZ\Publish\SPI\Persistence\Content;
use eZ\Publish\SPI\Persistence\Content\Location;
use eZ\Publish\SPI\Persistence\Content\Location\Handler as LocationHandler;

/**
 * Maps eZ Platform Content to an array of Document instances.
 *
 * @see \eZ\Publish\SPI\Persistence\Content
 * @see \Cabbage\SPI\Document
 */
final class Mapper
{
    /**
     * @var \eZ\Publish\SPI\Persistence\Content\Location\Handler
     */
    private $locationHandler;

    /**
     * @param \eZ\Publish\SPI\Persistence\Content\Location\Handler $locationHandler
     */
    public function __construct(LocationHandler $locationHandler)
    {
        $this->locationHandler = $locationHandler;
    }

    /**
     * @param \eZ\Publish\SPI\Persistence\Content $content
     *
     * @return \Cabbage\SPI\Document[]
     */
    public function map(Content $content): array
    {
        $documents = [];
        $locations = $this->locationHandler->loadLocationsByContent(
            $content->versionInfo->contentInfo->id
        );

        $documents[] = $this->mapContent($content);

        foreach ($locations as $location) {
            $documents[] = $this->mapLocation($location);
        }

        return $documents;
    }

    /**
     * @param \eZ\Publish\SPI\Persistence\Content $content
     *
     * @return \Cabbage\SPI\Document
     */
    private function mapContent(Content $content): Document
    {
        $fields = [
            new Field('test_keyword', 'value', new Keyword()),
            new Field('test_boolean', true, new Boolean()),
        ];

        return new Document(
            "content_{$content->versionInfo->contentInfo->id}",
            Document::TypeContent,
            $fields
        );
    }

    /**
     * @param \eZ\Publish\SPI\Persistence\Content\Location $location
     *
     * @return \Cabbage\SPI\Document
     */
    private function mapLocation(Location $location): Document
    {
        $fields = [
            new Field('test_keyword', 'value', new Keyword()),
            new Field('test_boolean', true, new Boolean()),
        ];

        return new Document(
            "location_{$location->id}",
            Document::TypeLocation,
            $fields
        );
    }
}
