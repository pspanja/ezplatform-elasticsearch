<?php

declare(strict_types=1);

namespace Cabbage\Core\Indexer;

use Cabbage\Core\Cluster\Configuration;
use Cabbage\Core\Indexer\FieldBuilders\Common;
use Cabbage\Core\Indexer\FieldBuilders\Content;
use Cabbage\Core\Indexer\FieldBuilders\Location;
use Cabbage\Core\Indexer\FieldBuilders\TranslationCommon;
use Cabbage\Core\Indexer\FieldBuilders\TranslationContent;
use Cabbage\Core\Indexer\FieldBuilders\TranslationLocation;
use Cabbage\SPI\Document;
use Cabbage\SPI\Document\Field;
use Cabbage\SPI\Document\Field\Type\Boolean;
use eZ\Publish\SPI\Persistence\Content as SPIContent;
use eZ\Publish\SPI\Persistence\Content\ContentInfo;
use eZ\Publish\SPI\Persistence\Content\Location as SPILocation;
use eZ\Publish\SPI\Persistence\Content\Location\Handler as LocationHandler;
use eZ\Publish\SPI\Persistence\Content\Type;
use eZ\Publish\SPI\Persistence\Content\Type\Handler as TypeHandler;
use RuntimeException;

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
     * @var \Cabbage\Core\Cluster\Configuration
     */
    private $configuration;

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
     * @param \Cabbage\Core\Cluster\Configuration $configuration
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
        Configuration $configuration,
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
        $this->configuration = $configuration;
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
        $contentInfo = $content->versionInfo->contentInfo;
        $type = $this->typeHandler->load($contentInfo->contentTypeId);
        $locations = $this->locationHandler->loadLocationsByContent($contentInfo->id);
        $commonFields = $this->getCommonFields($content, $type, $locations);
        $contentFields = $this->getContentFields($content, $type, $locations);
        $locationFieldsById = $this->mapLocationFieldsById($locations, $content, $type);
        $documentsGrouped = [[]];

        foreach ($content->versionInfo->languageCodes as $languageCode) {
            $translationCommonFields = $this->getTranslationCommonFields($languageCode, $content, $type, $locations);

            $documentsGrouped[] = $this->getLocationDocuments(
                $content,
                $type,
                $languageCode,
                $locationFieldsById,
                $commonFields,
                $translationCommonFields
            );

            $documentsGrouped[] = $this->getContentDocuments(
                $content,
                $languageCode,
                $commonFields,
                $contentFields,
                $translationCommonFields,
                $this->getTranslationContentFields($languageCode, $content, $type, $locations)
            );
        }

        return array_merge(...$documentsGrouped);
    }

    private function mapLocationFieldsById(
        array $locations,
        SPIContent $content,
        Type $type
    ): array
    {
        $map = [];

        foreach ($locations as $location) {
            $map[$location->id] = $this->getLocationFields($location, $content, $type);
        }

        return $map;
    }

    private function getContentDocuments(SPIContent $content, string $languageCode, ...$fields): array
    {
        $contentInfo = $content->versionInfo->contentInfo;
        $documents = [];

        if ($this->inDedicatedMainTranslationIndex($contentInfo, $languageCode)) {
            $documents[] = new Document(
                $this->idGenerator->generateContentDocumentId($content),
                $this->configuration->getIndexForMainTranslations(),
                array_merge(
                    $this->getTranslationIndexFields(false, true),
                    ...$fields
                )
            );
        }

        $documents[] = new Document(
            $this->idGenerator->generateContentDocumentId($content),
            $this->getIndexForLanguage($languageCode),
            array_merge(
                $this->getTranslationIndexFields(
                    true,
                    $this->inSharedMainTranslationIndex($contentInfo, $languageCode)
                ),
                ...$fields
            )
        );

        return $documents;
    }

    private function getTranslationIndexFields(bool $regularTranslationIndex, bool $mainTranslationIndex): array
    {
        return [
            new Field(
                'document_translation_index_regular',
                $regularTranslationIndex,
                new Boolean()
            ),
            new Field(
                'document_translation_index_main',
                $mainTranslationIndex,
                new Boolean()
            ),
        ];
    }

    private function getLocationDocuments(
        SPIContent $content,
        Type $type,
        string $languageCode,
        array $locationFieldsById,
        ...$fields
    ): array {
        $contentInfo = $content->versionInfo->contentInfo;
        $locations = $this->locationHandler->loadLocationsByContent($contentInfo->id);
        $documentsGrouped = [[]];

        foreach ($locations as $location) {
            $documentsGrouped[] = $this->getSingleLocationDocuments(
                $location,
                $content,
                $languageCode,
                array_merge(...$fields),
                $locationFieldsById[$location->id],
                $this->getTranslationLocationFields($languageCode, $location, $content, $type)
            );
        }

        return array_merge(...$documentsGrouped);
    }

    private function getSingleLocationDocuments(
        SPILocation $location,
        SPIContent $content,
        string $languageCode,
        ...$fields
    ): array {
        $contentInfo = $content->versionInfo->contentInfo;
        $documents = [];

        if ($this->inDedicatedMainTranslationIndex($contentInfo, $languageCode)) {
            $documents[] = new Document(
                $this->idGenerator->generateLocationDocumentId($location),
                $this->configuration->getIndexForMainTranslations(),
                array_merge(
                    $this->getTranslationIndexFields(false, true),
                    ...$fields
                )
            );
        }

        $documents[] = new Document(
            $this->idGenerator->generateLocationDocumentId($location),
            $this->getIndexForLanguage($languageCode),
            array_merge(
                $this->getTranslationIndexFields(
                    true,
                    $this->inSharedMainTranslationIndex($contentInfo, $languageCode)
                ),
                ...$fields
            )
        );

        return $documents;
    }

    private function inDedicatedMainTranslationIndex(ContentInfo $contentInfo, string $languageCode): bool
    {
        if ($contentInfo->mainLanguageCode !== $languageCode) {
            return false;
        }

        try {
            $mainTranslationIndex = $this->configuration->getIndexForMainTranslations();
        } catch (RuntimeException $e) {
            return false;
        }

        return $mainTranslationIndex !== $this->getIndexForLanguage($languageCode);
    }

    private function inSharedMainTranslationIndex(ContentInfo $contentInfo, string $languageCode): bool
    {
        if ($contentInfo->mainLanguageCode !== $languageCode) {
            return false;
        }

        try {
            $mainTranslationIndex = $this->configuration->getIndexForMainTranslations();
        } catch (RuntimeException $e) {
            return false;
        }

        return $mainTranslationIndex === $this->getIndexForLanguage($languageCode);
    }

    private function getIndexForLanguage(string $languageCode): string
    {
        if ($this->configuration->hasIndexForLanguage($languageCode)) {
            return $this->configuration->getIndexForLanguage($languageCode);
        }

        if ($this->configuration->hasDefaultIndex()) {
            return $this->configuration->getDefaultIndex();
        }

        throw new RuntimeException(
            "No index is configured for language code '{$languageCode}'"
        );
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

    private function getTranslationContentFields(string $languageCode, SPIContent $content, Type $type, array $locations): array
    {
        if ($this->translationContentFieldBuilder->accept($languageCode, $content, $type, $locations)) {
            return $this->translationContentFieldBuilder->build($languageCode, $content, $type, $locations);
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
