<?php

declare(strict_types=1);

namespace Cabbage;

use eZ\Publish\SPI\Persistence\Content;
use eZ\Publish\SPI\Persistence\Content\Location;
use eZ\Publish\SPI\Persistence\Content\Location\Handler as LocationHandler;

/**
 * Maps eZ Platform Content to an array of Document instances.
 *
 * @see \eZ\Publish\SPI\Persistence\Content
 * @see \Cabbage\Document
 */
final class DocumentMapper
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
     * @return \Cabbage\Document[]
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
     * @return \Cabbage\Document
     */
    private function mapContent(Content $content): Document
    {
        $fields = [
            new Field('test_string', 'value', 'string'),
            new Field('test_bool', true, 'bool'),
        ];

        return new Document(
            uniqid('content_', true),
            Document::TypeContent,
            $fields
        );
    }

    /**
     * @param \eZ\Publish\SPI\Persistence\Content\Location $location
     *
     * @return \Cabbage\Document
     */
    private function mapLocation(Location $location): Document
    {
        $fields = [
            new Field('test_string', 'value', 'string'),
            new Field('test_bool', true, 'bool'),
        ];

        return new Document(
            uniqid('location_', true),
            Document::TypeLocation,
            $fields
        );
    }
}
