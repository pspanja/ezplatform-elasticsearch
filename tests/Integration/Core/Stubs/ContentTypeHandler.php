<?php

declare(strict_types=1);

namespace Cabbage\Tests\Integration\Core\Stubs;

use eZ\Publish\SPI\Persistence\Content\Type;
use eZ\Publish\SPI\Persistence\Content\Type\CreateStruct;
use eZ\Publish\SPI\Persistence\Content\Type\FieldDefinition;
use eZ\Publish\SPI\Persistence\Content\Type\Group\CreateStruct as GroupCreateStruct;
use eZ\Publish\SPI\Persistence\Content\Type\Group\UpdateStruct as GroupUpdateStruct;
use eZ\Publish\SPI\Persistence\Content\Type\Handler as ContentTypeHandlerInterface;
use eZ\Publish\SPI\Persistence\Content\Type\UpdateStruct;
use RuntimeException;

class ContentTypeHandler implements ContentTypeHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function createGroup(GroupCreateStruct $group)
    {
        throw new RuntimeException('Not implemented');
    }

    /**
     * {@inheritdoc}
     */
    public function updateGroup(GroupUpdateStruct $group): void
    {
        throw new RuntimeException('Not implemented');
    }

    /**
     * {@inheritdoc}
     */
    public function deleteGroup($groupId): void
    {
        throw new RuntimeException('Not implemented');
    }

    /**
     * {@inheritdoc}
     */
    public function loadGroup($groupId)
    {
        throw new RuntimeException('Not implemented');
    }

    /**
     * {@inheritdoc}
     */
    public function loadGroups(array $groupIds)
    {
        throw new RuntimeException('Not implemented');
    }

    public function loadGroupByIdentifier($identifier)
    {
        throw new RuntimeException('Not implemented');
    }

    /**
     * {@inheritdoc}
     */
    public function loadAllGroups()
    {
        throw new RuntimeException('Not implemented');
    }

    /**
     * {@inheritdoc}
     */
    public function loadContentTypes($groupId, $status = Type::STATUS_DEFINED)
    {
        throw new RuntimeException('Not implemented');
    }

    /**
     * {@inheritdoc}
     */
    public function load($contentTypeId, $status = Type::STATUS_DEFINED): Type
    {
        return new Type();
    }

    /**
     * {@inheritdoc}
     */
    public function loadByIdentifier($identifier)
    {
        throw new RuntimeException('Not implemented');
    }

    /**
     * {@inheritdoc}
     */
    public function loadByRemoteId($remoteId)
    {
        throw new RuntimeException('Not implemented');
    }

    /**
     * {@inheritdoc}
     */
    public function create(CreateStruct $contentType)
    {
        throw new RuntimeException('Not implemented');
    }

    /**
     * {@inheritdoc}
     */
    public function update($contentTypeId, $status, UpdateStruct $contentType): void
    {
        throw new RuntimeException('Not implemented');
    }

    /**
     * {@inheritdoc}
     */
    public function delete($contentTypeId, $status): void
    {
        throw new RuntimeException('Not implemented');
    }

    /**
     * {@inheritdoc}
     */
    public function createDraft($modifierId, $contentTypeId)
    {
        throw new RuntimeException('Not implemented');
    }

    public function copy($userId, $contentTypeId, $status)
    {
        throw new RuntimeException('Not implemented');
    }

    /**
     * {@inheritdoc}
     */
    public function unlink($groupId, $contentTypeId, $status): void
    {
        throw new RuntimeException('Not implemented');
    }

    public function link($groupId, $contentTypeId, $status): void
    {
        throw new RuntimeException('Not implemented');
    }

    /**
     * {@inheritdoc}
     */
    public function getFieldDefinition($id, $status)
    {
        throw new RuntimeException('Not implemented');
    }

    /**
     * {@inheritdoc}
     */
    public function getContentCount($contentTypeId)
    {
        throw new RuntimeException('Not implemented');
    }

    /**
     * {@inheritdoc}
     */
    public function addFieldDefinition($contentTypeId, $status, FieldDefinition $fieldDefinition)
    {
        throw new RuntimeException('Not implemented');
    }

    /**
     * {@inheritdoc}
     */
    public function removeFieldDefinition($contentTypeId, $status, $fieldDefinitionId): void
    {
        throw new RuntimeException('Not implemented');
    }

    /**
     * {@inheritdoc}
     */
    public function updateFieldDefinition($contentTypeId, $status, FieldDefinition $fieldDefinition): void
    {
        throw new RuntimeException('Not implemented');
    }

    /**
     * {@inheritdoc}
     */
    public function publish($contentTypeId): void
    {
        throw new RuntimeException('Not implemented');
    }

    /**
     * {@inheritdoc}
     */
    public function getSearchableFieldMap()
    {
        throw new RuntimeException('Not implemented');
    }

    public function loadContentTypeList(array $contentTypeIds): array
    {
        throw new RuntimeException('Not implemented');
    }

    public function removeContentTypeTranslation(int $contentTypeId, string $languageCode): Type
    {
        throw new RuntimeException('Not implemented');
    }
}
