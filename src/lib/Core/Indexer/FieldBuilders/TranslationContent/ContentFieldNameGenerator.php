<?php

declare(strict_types=1);

namespace Cabbage\Core\Indexer\FieldBuilders\TranslationContent;

use eZ\Publish\SPI\Persistence\Content\Type\FieldDefinition;

/**
 * Generates Document Field name for a Content Field.
 */
final class ContentFieldNameGenerator
{
    /**
     * @param \eZ\Publish\SPI\Persistence\Content\Type\FieldDefinition $fieldDefinition
     * @param string $dataItemName
     *
     * @return string
     */
    public function generate(FieldDefinition $fieldDefinition, string $dataItemName): string
    {
        $elements = [
            $fieldDefinition->identifier,
            $fieldDefinition->fieldType,
            $dataItemName,
        ];

        return implode('_', $elements);
    }
}
