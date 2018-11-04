<?php

declare(strict_types=1);

namespace Cabbage\Core\FieldType\DataMapper;

use Cabbage\SPI\Document\Field\Type\Keyword;
use Cabbage\SPI\FieldType\DataItem;
use Cabbage\SPI\FieldType\DataMapper;
use eZ\Publish\SPI\Persistence\Content\Field;
use eZ\Publish\SPI\Persistence\Content\Type\FieldDefinition;

final class TextLine extends DataMapper
{
    /**
     * Main field holding the text value as a keyword.
     */
    public const FieldValue = 'value';

    public function map(Field $field, FieldDefinition $fieldDefinition): array
    {
        return [
            new DataItem(
                self::FieldValue,
                $field->value->data,
                new Keyword()
            ),
        ];
    }
}
