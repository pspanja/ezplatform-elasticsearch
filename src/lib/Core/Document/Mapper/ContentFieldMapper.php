<?php

declare(strict_types=1);

namespace Cabbage\Core\Document\Mapper;

use Cabbage\SPI\Field;
use Cabbage\SPI\FieldType\Boolean;
use Cabbage\SPI\FieldType\Keyword;
use eZ\Publish\SPI\Persistence\Content;
use eZ\Publish\SPI\Persistence\Content\Field as ContentField;
use eZ\Publish\SPI\Persistence\Content\Type;
use eZ\Publish\SPI\Persistence\Content\Type\FieldDefinition;
use RuntimeException;

/**
 * Maps eZ Platform Content Fields to search Fields for indexing.
 *
 * @see \eZ\Publish\SPI\Persistence\Content\Field
 * @see \Cabbage\SPI\Field
 */
final class ContentFieldMapper
{
    /**
     * @param \eZ\Publish\SPI\Persistence\Content $content
     * @param \eZ\Publish\SPI\Persistence\Content\Type $type
     *
     * @return \Cabbage\SPI\Field[]
     */
    public function map(Content $content, Type $type): array
    {
        $indexFields = [];

        $fieldDefinitionMapById = $this->mapFieldDefinitionsById($type);

        foreach ($content->fields as $field) {
            $fieldDefinition = $this->getFieldDefinition($field, $fieldDefinitionMapById);

            switch ($field->type) {
                case 'ezboolean':
                    $indexFields[] = new Field(
                        $fieldDefinition->identifier,
                        $field->value->data,
                        new Boolean()
                    );

                    break;
                case 'ezstring':
                    $indexFields[] = new Field(
                        $fieldDefinition->identifier,
                        $field->value->data,
                        new Keyword()
                    );

                    break;
                default:
                    throw new RuntimeException(
                        "Field of type '{$field->type}' is not handled"
                    );
            }
        }

        return $indexFields;
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
            $map[$fieldDefinition->identifier] = $fieldDefinition;
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
