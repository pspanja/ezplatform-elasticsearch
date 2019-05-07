<?php

declare(strict_types=1);

namespace Cabbage\Core;

use Cabbage\SPI\Index;
use Cabbage\SPI\LanguageFilter;
use RuntimeException;

/**
 * Represents Elasticsearch cluster configuration for eZ Platform Repository.
 */
final class Cluster
{
    /**
     * @var \Cabbage\SPI\Node[]
     */
    private $coordinatingNodes;

    /**
     * @var \Cabbage\SPI\Index[]
     */
    private $indexByLanguageCode;

    /**
     * @var ?\Cabbage\SPI\Index
     */
    private $indexForMainTranslations;

    /**
     * @var ?\Cabbage\SPI\Index
     */
    private $defaultIndex;

    /**
     * @param \Cabbage\SPI\Node[] $coordinatingNodes
     * @param \Cabbage\SPI\Index[] $indexByLanguageCode
     * @param \Cabbage\SPI\Index $indexForMainTranslations
     * @param \Cabbage\SPI\Index $defaultIndex
     */
    public function __construct(
        array $coordinatingNodes,
        array $indexByLanguageCode,
        ?Index $indexForMainTranslations,
        ?Index $defaultIndex
    ) {
        $this->coordinatingNodes = $coordinatingNodes;
        $this->defaultIndex = $defaultIndex;
        $this->indexForMainTranslations = $indexForMainTranslations;
        $this->indexByLanguageCode = $indexByLanguageCode;
    }

    public function hasDefaultIndex(): bool
    {
        return $this->defaultIndex instanceof Index;
    }

    public function getDefaultIndex(): Index
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
        return $this->indexForMainTranslations instanceof Index;
    }

    public function getIndexForMainTranslations(): Index
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

    public function getIndexForLanguage(string $languageCode): Index
    {
        if ($this->hasIndexForLanguage($languageCode)) {
            return $this->indexByLanguageCode[$languageCode];
        }

        throw new RuntimeException(
            "Index for language with code '{$languageCode}' is not defined"
        );
    }

    public function getIndicesForAllLanguages(): array
    {
        return array_values($this->indexByLanguageCode);
    }

    public function getCoordinatingNodes(): array
    {
        return $this->coordinatingNodes;
    }
}
