<?php

declare(strict_types=1);

namespace Cabbage\Core\Document\Mapper\ContentField;

use Cabbage\Core\FieldType\DataMapperRegistry;
use Cabbage\SPI\Document\Field as DocumentField;
use eZ\Publish\SPI\Persistence\Content;
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
final class Mapper
{
    /**
     * @var \Cabbage\Core\FieldType\DataMapperRegistry
     */
    private $dataMapperRegistry;
    /**
     * @var \Cabbage\Core\Document\Mapper\ContentField\NameGenerator
     */
    private $nameGenerator;

    /**
     * @param \Cabbage\Core\FieldType\DataMapperRegistry $dataMapperRegistry
     * @param \Cabbage\Core\Document\Mapper\ContentField\NameGenerator $nameGenerator
     */
    public function __construct(
        DataMapperRegistry $dataMapperRegistry,
        NameGenerator $nameGenerator
    ) {
        $this->dataMapperRegistry = $dataMapperRegistry;
        $this->nameGenerator = $nameGenerator;
    }

    /**
     * @param \eZ\Publish\SPI\Persistence\Content $content
     * @param \eZ\Publish\SPI\Persistence\Content\Type $type
     *
     * @return \Cabbage\SPI\Document\Field[]
     */
    public function map(Content $content, Type $type): array
    {
        $documentFieldGrouped = [[]];

        $fieldDefinitionMapById = $this->mapFieldDefinitionsById($type);

        foreach ($content->fields as $field) {
            $fieldDefinition = $this->getFieldDefinition($field, $fieldDefinitionMapById);
            $documentFieldGrouped[] = $this->mapField($field, $fieldDefinition);
        }

        return array_merge(...$documentFieldGrouped);
    }

    private function mapField(ContentField $field, FieldDefinition $fieldDefinition): array
    {
        $namedDocumentFields = [];
        $dataMapper = $this->dataMapperRegistry->get($field->type);
        $dataItems = $dataMapper->map($field, $fieldDefinition);

        foreach ($dataItems as $dataItem) {
            $namedDocumentFields[] = new DocumentField(
                $this->nameGenerator->generate($fieldDefinition, $dataItem->name),
                $dataItem->value,
                $dataItem->type
            );
        }

        return $namedDocumentFields;
    }

    /**
     * @param \eZ\Publish\SPI\Persistence\Content\Type $type
     *
     * @return \eZ\Publish\SPI\Persistence\Content\Type\FieldDefinition[]
     */
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
            "Couldn't find field definition with ID '{$field->fieldDefinitionId}'"
        );
    }
}
