<?php

declare(strict_types=1);

namespace Cabbage\Core\Indexer\FieldBuilders\TranslationContent;

use Cabbage\SPI\FieldType\DataItem;
use eZ\Publish\SPI\Persistence\Content\Type\FieldDefinition;

/**
 * Generates Document Field name for a Content Field.
 */
final class ContentFieldNameGenerator
{
    /**
     * @param \eZ\Publish\SPI\Persistence\Content\Type\FieldDefinition $fieldDefinition
     * @param \Cabbage\SPI\FieldType\DataItem $dataItem
     *
     * @return string
     */
    public function generate(FieldDefinition $fieldDefinition, DataItem $dataItem): string
    {
        $elements = [
            $fieldDefinition->identifier,
            $fieldDefinition->fieldType,
            $dataItem->name,
        ];

        return implode('_', $elements);
    }
}
