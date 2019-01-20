<?php

declare(strict_types=1);

namespace Cabbage\Core\Document\Field;

use Cabbage\SPI\Document\Field;

/**
 * Generates name of the Document Field with the type suffix.
 *
 * Type suffix is used by Elasticsearch to determine the type of the Field's value.
 *
 * @see \Cabbage\SPI\Document\Field::$type
 */
final class TypedNameGenerator
{
    /**
     * @var string[]
     */
    private $suffixMap;

    /**
     * @param string[] $suffixMap
     */
    public function __construct(array $suffixMap)
    {
        $this->suffixMap = $suffixMap;
    }

    /**
     * Generate field name for the Elasticsearch index.
     *
     * @param \Cabbage\SPI\Document\Field $field
     *
     * @return string
     */
    public function generate(Field $field): string
    {
        $typeSuffix = $this->getTypeSuffix($field->type->identifier);

        return "{$field->name}_{$typeSuffix}";
    }

    private function getTypeSuffix(string $typeIdentifier): string
    {
        if (array_key_exists($typeIdentifier, $this->suffixMap)) {
            return $this->suffixMap[$typeIdentifier];
        }

        return $typeIdentifier;
    }
}
