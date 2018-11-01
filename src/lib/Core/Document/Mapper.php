<?php

declare(strict_types=1);

namespace Cabbage\Core\Document;

use Cabbage\Core\Document\Mapper\ContentFieldMapper;
use Cabbage\SPI\Document;
use Cabbage\SPI\Field;
use Cabbage\SPI\FieldType\Boolean;
use Cabbage\SPI\FieldType\Keyword;
use eZ\Publish\SPI\Persistence\Content;
use eZ\Publish\SPI\Persistence\Content\Location;
use eZ\Publish\SPI\Persistence\Content\Location\Handler as LocationHandler;
use eZ\Publish\SPI\Persistence\Content\Type\Handler as TypeHandler;

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
     * @var \eZ\Publish\SPI\Persistence\Content\Type\Handler
     */
    private $typeHandler;

    /**
     * @var \Cabbage\Core\Document\Mapper\ContentFieldMapper
     */
    private $contentFieldMapper;

    /**
     * @param \eZ\Publish\SPI\Persistence\Content\Location\Handler $locationHandler
     * @param \eZ\Publish\SPI\Persistence\Content\Type\Handler $typeHandler
     * @param \Cabbage\Core\Document\Mapper\ContentFieldMapper $contentFieldMapper
     */
    public function __construct(
        LocationHandler $locationHandler,
        TypeHandler $typeHandler,
        ContentFieldMapper $contentFieldMapper
    ) {
        $this->locationHandler = $locationHandler;
        $this->typeHandler = $typeHandler;
        $this->contentFieldMapper = $contentFieldMapper;
    }

    /**
     * @param \eZ\Publish\SPI\Persistence\Content $content
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
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
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     *
     * @return \Cabbage\SPI\Document
     */
    private function mapContent(Content $content): Document
    {
        $fieldsGrouped = [[]];
        $type = $this->typeHandler->load($content->versionInfo->contentInfo->contentTypeId);

        $fieldsGrouped[] = $this->contentFieldMapper->map($content, $type);
        $fieldsGrouped[] = [
            new Field('test', 'value', new Keyword()),
            new Field('test', true, new Boolean()),
        ];

        return new Document(
            "content_{$content->versionInfo->contentInfo->id}",
            Document::TypeContent,
            array_merge(...$fieldsGrouped)
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
            new Field('test', 'value', new Keyword()),
            new Field('test', true, new Boolean()),
        ];

        return new Document(
            "location_{$location->id}",
            Document::TypeLocation,
            $fields
        );
    }
}
