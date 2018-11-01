<?php

declare(strict_types=1);

namespace Cabbage\Core\Document;

use Cabbage\SPI\Document;
use Cabbage\SPI\Field;
use Cabbage\SPI\FieldType\Boolean;
use Cabbage\SPI\FieldType\Keyword;
use eZ\Publish\SPI\Persistence\Content;
use eZ\Publish\SPI\Persistence\Content\Field as ContentField;
use eZ\Publish\SPI\Persistence\Content\Location;
use eZ\Publish\SPI\Persistence\Content\Location\Handler as LocationHandler;
use eZ\Publish\SPI\Persistence\Content\Type;
use eZ\Publish\SPI\Persistence\Content\Type\FieldDefinition;
use eZ\Publish\SPI\Persistence\Content\Type\Handler as TypeHandler;
use RuntimeException;

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
     * @param \eZ\Publish\SPI\Persistence\Content\Location\Handler $locationHandler
     * @param \eZ\Publish\SPI\Persistence\Content\Type\Handler $typeHandler
     */
    public function __construct(LocationHandler $locationHandler, TypeHandler $typeHandler)
    {
        $this->locationHandler = $locationHandler;
        $this->typeHandler = $typeHandler;
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

        $fieldsGrouped[] = $this->mapContentFields($content);
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

    /**
     * @param \eZ\Publish\SPI\Persistence\Content $content
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     *
     * @return \Cabbage\SPI\Field[]
     */
    private function mapContentFields(Content $content): array
    {
        $indexFields = [];

        $type = $this->typeHandler->load($content->versionInfo->contentInfo->contentTypeId);
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
