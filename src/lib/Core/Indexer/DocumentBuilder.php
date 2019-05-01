<?php

declare(strict_types=1);

namespace Cabbage\Core\Indexer;

use Cabbage\Core\Indexer\FieldBuilders\Common;
use Cabbage\Core\Indexer\FieldBuilders\Content;
use Cabbage\Core\Indexer\FieldBuilders\Location;
use Cabbage\Core\Indexer\FieldBuilders\TranslationCommon;
use Cabbage\Core\Indexer\FieldBuilders\TranslationContent;
use Cabbage\Core\Indexer\FieldBuilders\TranslationLocation;
use Cabbage\SPI\Document;
use Cabbage\SPI\Document\Field;
use Cabbage\SPI\Document\Field\Type\Identifier;
use eZ\Publish\SPI\Persistence\Content as SPIContent;
use eZ\Publish\SPI\Persistence\Content\Location as SPILocation;
use eZ\Publish\SPI\Persistence\Content\Location\Handler as LocationHandler;
use eZ\Publish\SPI\Persistence\Content\Type;
use eZ\Publish\SPI\Persistence\Content\Type\Handler as TypeHandler;

/**
 * Maps eZ Platform Content to an array of Document instances.
 *
 * @see \eZ\Publish\SPI\Persistence\Content
 * @see \Cabbage\SPI\Document
 */
final class DocumentBuilder
{
    /**
     * Content document type identifier.
     *
     * @var string
     */
    public const TypeContent = 'content';

    /**
     * Location document type identifier.
     *
     * @var string
     */
    public const TypeLocation = 'location';

    /**
     * @var \eZ\Publish\SPI\Persistence\Content\Location\Handler
     */
    private $locationHandler;

    /**
     * @var \eZ\Publish\SPI\Persistence\Content\Type\Handler
     */
    private $typeHandler;

    /**
     * @var \Cabbage\Core\Indexer\FieldBuilders\Common
     */
    private $common;

    /**
     * @var \Cabbage\Core\Indexer\FieldBuilders\Content
     */
    private $contentFieldBuilder;

    /**
     * @var \Cabbage\Core\Indexer\FieldBuilders\Location
     */
    private $locationFieldBuilder;

    /**
     * @var \Cabbage\Core\Indexer\FieldBuilders\TranslationCommon
     */
    private $translationCommonFieldBuilder;

    /**
     * @var \Cabbage\Core\Indexer\FieldBuilders\TranslationContent
     */
    private $translationContentFieldBuilder;

    /**
     * @var \Cabbage\Core\Indexer\FieldBuilders\TranslationLocation
     */
    private $translationLocationFieldBuilder;

    /**
     * @var \Cabbage\Core\Indexer\DocumentIdGenerator
     */
    private $idGenerator;

    /**
     * @param \eZ\Publish\SPI\Persistence\Content\Location\Handler $locationHandler
     * @param \eZ\Publish\SPI\Persistence\Content\Type\Handler $typeHandler
     * @param \Cabbage\Core\Indexer\FieldBuilders\Common $common
     * @param \Cabbage\Core\Indexer\FieldBuilders\Content $contentFieldBuilder
     * @param \Cabbage\Core\Indexer\FieldBuilders\Location $locationFieldBuilder
     * @param \Cabbage\Core\Indexer\FieldBuilders\TranslationCommon $translationCommonFieldBuilder
     * @param \Cabbage\Core\Indexer\FieldBuilders\TranslationContent $translationContentFieldBuilder
     * @param \Cabbage\Core\Indexer\FieldBuilders\TranslationLocation $translationLocationFieldBuilder
     * @param \Cabbage\Core\Indexer\DocumentIdGenerator $idGenerator
     */
    public function __construct(
        LocationHandler $locationHandler,
        TypeHandler $typeHandler,
        Common $common,
        Content $contentFieldBuilder,
        Location $locationFieldBuilder,
        TranslationCommon $translationCommonFieldBuilder,
        TranslationContent $translationContentFieldBuilder,
        TranslationLocation $translationLocationFieldBuilder,
        DocumentIdGenerator $idGenerator
    ) {
        $this->locationHandler = $locationHandler;
        $this->typeHandler = $typeHandler;
        $this->common = $common;
        $this->contentFieldBuilder = $contentFieldBuilder;
        $this->locationFieldBuilder = $locationFieldBuilder;
        $this->translationCommonFieldBuilder = $translationCommonFieldBuilder;
        $this->translationContentFieldBuilder = $translationContentFieldBuilder;
        $this->translationLocationFieldBuilder = $translationLocationFieldBuilder;
        $this->idGenerator = $idGenerator;
    }

    /**
     * @param \eZ\Publish\SPI\Persistence\Content $content
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     *
     * @return \Cabbage\SPI\Document[]
     */
    public function build(SPIContent $content): array
    {
        $documents = [];
        $type = $this->typeHandler->load($content->versionInfo->contentInfo->contentTypeId);
        $locations = $this->locationHandler->loadLocationsByContent(
            $content->versionInfo->contentInfo->id
        );

        $documents[] = $this->buildContentDocument($content, $type);

        foreach ($locations as $location) {
            $documents[] = $this->buildLocationDocument($location, $content, $type);
        }

        return $documents;
    }

    /**
     * @param \eZ\Publish\SPI\Persistence\Content $content
     * @param \eZ\Publish\SPI\Persistence\Content\Type $type
     *
     * @return \Cabbage\SPI\Document
     */
    private function buildContentDocument(SPIContent $content, Type $type): Document
    {
        $fieldsGrouped = [[]];

        $commonMetadataFields = [
            new Field(
                'type',
                self::TypeContent,
                new Identifier()
            ),
        ];

        $contentMetadataFields = [
            new Field(
                'content_id',
                $content->versionInfo->contentInfo->id,
                new Identifier()
            ),
        ];

        $fieldsGrouped[] = $commonMetadataFields;
        $fieldsGrouped[] = $contentMetadataFields;
        $fieldsGrouped[] = $this->translationContentFieldBuilder->build($content, $type);

        return new Document(
            $this->idGenerator->generateContentDocumentId($content),
            array_merge(...$fieldsGrouped)
        );
    }

    /**
     * @param \eZ\Publish\SPI\Persistence\Content\Location $location
     * @param \eZ\Publish\SPI\Persistence\Content $content
     * @param \eZ\Publish\SPI\Persistence\Content\Type $type
     *
     * @return \Cabbage\SPI\Document
     */
    private function buildLocationDocument(SPILocation $location, SPIContent $content, Type $type): Document
    {
        $fieldsGrouped = [[]];

        $commonMetadataFields = [
            new Field(
                'type',
                self::TypeLocation,
                new Identifier()
            ),
        ];

        $contentMetadataFields = [
            new Field(
                'content_id',
                $content->versionInfo->contentInfo->id,
                new Identifier()
            ),
        ];

        $locationMetadataFields = [
            new Field(
                'location_id',
                $location->id,
                new Identifier()
            ),
        ];

        $fieldsGrouped[] = $commonMetadataFields;
        $fieldsGrouped[] = $contentMetadataFields;
        $fieldsGrouped[] = $locationMetadataFields;
        $fieldsGrouped[] = $this->translationContentFieldBuilder->build($content, $type);

        return new Document(
            $this->idGenerator->generateLocationDocumentId($location),
            array_merge(...$fieldsGrouped)
        );
    }
}
