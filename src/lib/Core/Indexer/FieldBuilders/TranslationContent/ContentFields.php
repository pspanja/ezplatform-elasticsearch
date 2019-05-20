<?php

declare(strict_types=1);

namespace Cabbage\Core\Indexer\FieldBuilders\TranslationContent;

use Cabbage\Core\FieldType\DataMapperRegistry;
use Cabbage\Core\Indexer\FieldBuilders\TranslationContent;
use Cabbage\SPI\Document\Field as DocumentField;
use eZ\Publish\SPI\Persistence\Content as SPIContent;
use eZ\Publish\SPI\Persistence\Content\Field as ContentField;
use eZ\Publish\SPI\Persistence\Content\Type;
use eZ\Publish\SPI\Persistence\Content\Type\FieldDefinition;
use RuntimeException;

/**
 * Maps eZ Platform Content Fields to Document Fields.
 *
 * @see \eZ\Publish\SPI\Persistence\Content\Field
 * @see \Cabbage\SPI\Document\Field
 */
final class ContentFields extends TranslationContent
{
    /**
     * @var \Cabbage\Core\FieldType\DataMapperRegistry
     */
    private $dataMapperRegistry;
    /**
     * @var \Cabbage\Core\Indexer\FieldBuilders\TranslationContent\ContentFieldNameGenerator
     */
    private $nameGenerator;

    /**
     * @param \Cabbage\Core\FieldType\DataMapperRegistry $dataMapperRegistry
     * @param \Cabbage\Core\Indexer\FieldBuilders\TranslationContent\ContentFieldNameGenerator
     */
    public function __construct(
        DataMapperRegistry $dataMapperRegistry,
        ContentFieldNameGenerator $nameGenerator
    ) {
        $this->dataMapperRegistry = $dataMapperRegistry;
        $this->nameGenerator = $nameGenerator;
    }

    public function accept(string $languageCode, SPIContent $content, Type $type, array $locations): bool
    {
        return true;
    }

    public function build(string $languageCode, SPIContent $content, Type $type, array $locations): array
    {
        $documentFieldGrouped = [[]];

        $fieldDefinitionMapById = $this->mapFieldDefinitionsById($type);

        foreach ($content->fields as $field) {
            if ($field->languageCode !== $languageCode) {
                continue;
            }

            $fieldDefinition = $this->getFieldDefinition($field, $fieldDefinitionMapById);
            $documentFieldGrouped[] = $this->buildFields($field, $fieldDefinition);
        }

        return array_merge(...$documentFieldGrouped);
    }

    private function buildFields(ContentField $field, FieldDefinition $fieldDefinition): array
    {
        $namedDocumentFields = [];
        $dataMapper = $this->dataMapperRegistry->get($field->type);
        $dataItems = $dataMapper->map($field, $fieldDefinition);

        foreach ($dataItems as $dataItem) {
            $namedDocumentFields[] = new DocumentField(
                $this->nameGenerator->generate($fieldDefinition, $dataItem),
                $dataItem->value,
                $dataItem->type
            );
        }

        return $namedDocumentFields;
    }

    private function mapFieldDefinitionsById(Type $type): array
    {
        $map = [];

        foreach ($type->fieldDefinitions as $fieldDefinition) {
            $map[$fieldDefinition->id] = $fieldDefinition;
        }

        return $map;
    }

    /**
     * @param \eZ\Publish\SPI\Persistence\Content\Field $field
     * @param array $fieldDefinitionMapById
     *
     * @return \eZ\Publish\SPI\Persistence\Content\Type\FieldDefinition
     */
    private function getFieldDefinition(
        ContentField $field,
        array $fieldDefinitionMapById
    ): FieldDefinition {
        if (array_key_exists($field->fieldDefinitionId, $fieldDefinitionMapById)) {
            return $fieldDefinitionMapById[$field->fieldDefinitionId];
        }

        throw new RuntimeException(
            'Could not find field definition with ID "' . $field->fieldDefinitionId . '"'
        );
    }
}
