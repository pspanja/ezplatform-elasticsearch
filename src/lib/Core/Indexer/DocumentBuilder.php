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
    private $commonFieldBuilder;

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
     * @param \Cabbage\Core\Indexer\FieldBuilders\Common $commonFieldBuilder
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
        Common $commonFieldBuilder,
        Content $contentFieldBuilder,
        Location $locationFieldBuilder,
        TranslationCommon $translationCommonFieldBuilder,
        TranslationContent $translationContentFieldBuilder,
        TranslationLocation $translationLocationFieldBuilder,
        DocumentIdGenerator $idGenerator
    ) {
        $this->locationHandler = $locationHandler;
        $this->typeHandler = $typeHandler;
        $this->commonFieldBuilder = $commonFieldBuilder;
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
        $type = $this->typeHandler->load($content->versionInfo->contentInfo->contentTypeId);
        $locations = $this->locationHandler->loadLocationsByContent($content->versionInfo->contentInfo->id);
        $contentDocuments = [];
        $locationDocuments = [];
        $commonFields = $this->getCommonFields($content, $type, $locations);
        $contentFields = $this->getContentFields($content, $type, $locations);
        $locationFieldsById = [];

        foreach ($locations as $location) {
            $locationFieldsById[$location->id] = $this->getLocationFields($location, $content, $type);
        }

        foreach ($content->versionInfo->languageCodes as $languageCode) {
            $translationCommonFields = $this->getTranslationCommonFields($languageCode, $content, $type, $locations);
            $translationContentFields = $this->getTranslationContentFields($content, $type, $locations);

            foreach ($locations as $location) {
                $translationLocationFields = $this->getTranslationLocationFields($languageCode, $location, $content, $type);

                $locationDocuments[] = new Document(
                    $this->idGenerator->generateLocationDocumentId($location),
                    self::TypeLocation,
                    array_merge(
                        $commonFields,
                        $locationFieldsById[$location->id],
                        $translationCommonFields,
                        $translationLocationFields
                    )
                );
            }

            $contentDocuments[] = new Document(
                $this->idGenerator->generateContentDocumentId($content),
                self::TypeContent,
                array_merge(
                    $commonFields,
                    $contentFields,
                    $translationCommonFields,
                    $translationContentFields
                )
            );
        }

        return array_merge($contentDocuments, $locationDocuments);
    }

    private function getCommonFields(SPIContent $content, Type $type, array $locations): array
    {
        if ($this->commonFieldBuilder->accept($content, $type, $locations)) {
            return $this->commonFieldBuilder->build($content, $type, $locations);
        }

        return [];
    }

    private function getContentFields(SPIContent $content, Type $type, array $locations): array
    {
        if ($this->contentFieldBuilder->accept($content, $type, $locations)) {
            return $this->contentFieldBuilder->build($content, $type, $locations);
        }

        return [];
    }

    private function getLocationFields(SPILocation $location, SPIContent $content, Type $type): array
    {
        if ($this->locationFieldBuilder->accept($location, $content, $type)) {
            return $this->locationFieldBuilder->build($location, $content, $type);
        }

        return [];
    }

    private function getTranslationCommonFields(string $languageCode, SPIContent $content, Type $type, array $locations): array
    {
        if ($this->translationCommonFieldBuilder->accept($languageCode, $content, $type, $locations)) {
            return $this->translationCommonFieldBuilder->build($languageCode, $content, $type, $locations);
        }

        return [];
    }

    private function getTranslationContentFields(SPIContent $content, Type $type, array $locations): array
    {
        if ($this->translationContentFieldBuilder->accept($content, $type, $locations)) {
            return $this->translationContentFieldBuilder->build($content, $type, $locations);
        }

        return [];
    }

    private function getTranslationLocationFields(string $languageCode, SPILocation $location, SPIContent $content, Type $type): array
    {
        if ($this->translationLocationFieldBuilder->accept($languageCode, $location, $content, $type)) {
            return $this->translationLocationFieldBuilder->build($languageCode, $location, $content, $type);
        }

        return [];
    }
}
