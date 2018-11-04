<?php

declare(strict_types=1);

namespace Cabbage\Core\Document\Mapper;

use eZ\Publish\SPI\Persistence\Content\Type\FieldDefinition;

/**
 * Generates Document Field name for a Content Field.
 */
final class ContentFieldNameGenerator
{
    /**
     * @param \eZ\Publish\SPI\Persistence\Content\Type\FieldDefinition $fieldDefinition
     * @param string $valueName
     *
     * @return string
     */
    public function generate(FieldDefinition $fieldDefinition, string $valueName): string
    {
        $elements = [
            $fieldDefinition->identifier,
            $fieldDefinition->fieldType,
            $valueName,
        ];

        return implode('_', $elements);
    }
}
