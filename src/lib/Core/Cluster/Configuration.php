<?php

declare(strict_types=1);

namespace Cabbage\Core\Cluster;

use RuntimeException;

/**
 * Represents Elasticsearch cluster configuration for eZ Platform Repository.
 */
final class Configuration
{
    /**
     * @var \Cabbage\SPI\Node[]
     */
    private $coordinatingNodes;

    /**
     * @var string[]
     */
    private $indexByLanguageCode;

    /**
     * @var ?string
     */
    private $indexForMainTranslations;

    /**
     * @var ?string
     */
    private $defaultIndex;

    /**
     * @param \Cabbage\SPI\Node[] $coordinatingNodes
     * @param string[] $indexByLanguageCode
     * @param string $indexForMainTranslations
     * @param string $defaultIndex
     */
    public function __construct(
        array $coordinatingNodes,
        array $indexByLanguageCode,
        ?string $indexForMainTranslations,
        ?string $defaultIndex
    ) {
        $this->coordinatingNodes = $coordinatingNodes;
        $this->indexByLanguageCode = $indexByLanguageCode;
        $this->indexForMainTranslations = $indexForMainTranslations;
        $this->defaultIndex = $defaultIndex;
    }

    public function hasDefaultIndex(): bool
    {
        return $this->defaultIndex !== null;
    }

    public function getDefaultIndex(): string
    {
        if ($this->hasDefaultIndex()) {
            return $this->defaultIndex;
        }

        throw new RuntimeException(
            'Default index is not defined'
        );
    }

    public function hasIndexForMainTranslations(): bool
    {
        return $this->indexForMainTranslations !== null;
    }

    public function getIndexForMainTranslations(): string
    {
        if ($this->hasIndexForMainTranslations()) {
            return $this->indexForMainTranslations;
        }

        throw new RuntimeException(
            'Index for main translations is not defined'
        );
    }

    public function hasIndexForLanguage(string $languageCode): bool
    {
        return array_key_exists($languageCode, $this->indexByLanguageCode);
    }

    public function getIndexForLanguage(string $languageCode): string
    {
        if ($this->hasIndexForLanguage($languageCode)) {
            return $this->indexByLanguageCode[$languageCode];
        }

        throw new RuntimeException(
            "Index for language with code '{$languageCode}' is not defined"
        );
    }

    public function getAllIndices(): array
    {
        $indexSet = array_flip($this->indexByLanguageCode);

        if ($this->hasDefaultIndex()) {
            $indexSet[$this->getDefaultIndex()] = true;
        }

        return array_keys($indexSet);
    }

    public function getCoordinatingNodes(): array
    {
        return $this->coordinatingNodes;
    }
}
