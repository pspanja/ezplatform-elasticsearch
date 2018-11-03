<?php

declare(strict_types=1);

namespace Cabbage\Core\FieldType\DataMapper;

use Cabbage\SPI\Document\Field as DocumentField;
use Cabbage\SPI\Document\Field\Type\Boolean;
use Cabbage\SPI\FieldType\DataMapper;
use eZ\Publish\SPI\Persistence\Content\Field;
use eZ\Publish\SPI\Persistence\Content\Type\FieldDefinition;

final class Checkbox extends DataMapper
{
    /**
     * Main field holding the checkbox value as a boolean.
     */
    public const DataValue = 'value';

    public function map(Field $field, FieldDefinition $fieldDefinition): array
    {
        return [
            new DocumentField(
                self::DataValue,
                $field->value->data,
                new Boolean()
            ),
        ];
    }
}
