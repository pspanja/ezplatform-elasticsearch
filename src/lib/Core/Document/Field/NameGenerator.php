<?php

declare(strict_types=1);

namespace Cabbage\Core\Document\Field;

use Cabbage\SPI\Document\Field;

/**
 * Generates the name of the field in the search engine.
 *
 * @see \Cabbage\SPI\Field
 */
final class NameGenerator
{
    /**
     * @var string[]
     */
    private $typeSuffixMap;

    /**
     * @param string[] $typeSuffixMap
     */
    public function __construct(array $typeSuffixMap)
    {
        $this->typeSuffixMap = $typeSuffixMap;
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
        if (array_key_exists($typeIdentifier, $this->typeSuffixMap)) {
            return $this->typeSuffixMap[$typeIdentifier];
        }

        return $typeIdentifier;
    }
}
