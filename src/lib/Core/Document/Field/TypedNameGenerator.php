<?php

declare(strict_types=1);

namespace Cabbage\Core\Document\Field;

use Cabbage\SPI\Document\Field;

/**
 * Generates typed name of the Document Field.
 *
 * @see \Cabbage\SPI\Document\Field
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
