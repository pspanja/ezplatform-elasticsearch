<?php

declare(strict_types=1);

namespace Cabbage\Core\FieldType\DocumentFieldMapper;

use Cabbage\SPI\Document\Field as DocumentField;
use Cabbage\SPI\Document\Field\Type\Boolean;
use Cabbage\SPI\FieldType\DocumentFieldMapper;
use eZ\Publish\SPI\Persistence\Content\Field;
use eZ\Publish\SPI\Persistence\Content\Type\FieldDefinition;

final class Checkbox extends DocumentFieldMapper
{
    /**
     * Main field holding the checkbox value as a boolean.
     */
    public const FieldValue = 'value';

    public function map(Field $field, FieldDefinition $fieldDefinition): array
    {
        return [
            new DocumentField(
                self::FieldValue,
                $field->value->data,
                new Boolean()
            ),
        ];
    }
}
