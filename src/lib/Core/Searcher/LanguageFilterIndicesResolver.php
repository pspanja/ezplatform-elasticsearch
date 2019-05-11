<?php

declare(strict_types=1);

namespace Cabbage\Core\Searcher;

use Cabbage\Core\Cluster\Configuration;
use Cabbage\SPI\LanguageFilter;
use RuntimeException;

/**
 * Matches a LanguageFilter to an array of indices.
 *
 * @see \Cabbage\SPI\LanguageFilter
 */
final class LanguageFilterIndicesResolver
{
    /**
     * @var \Cabbage\Core\Cluster\Configuration
     */
    private $configuration;

    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * @param \Cabbage\SPI\LanguageFilter $languageFilter
     *
     * @return string[]
     */
    public function resolve(LanguageFilter $languageFilter): array
    {
        $indices = $this->resolveIndices($languageFilter);

        if (empty($indices)) {
            throw new RuntimeException(
                'No indices are configured for the given LanguageFilter'
            );
        }

        return $indices;
    }

    /**
     * @param \Cabbage\SPI\LanguageFilter $languageFilter
     *
     * @return string[]
     */
    private function resolveIndices(LanguageFilter $languageFilter): array
    {
        if ($this->configuration->hasIndexForMainTranslations()) {
            return $this->resolveWithIndexForMainTranslations($languageFilter);
        }

        return $this->resolveWithoutIndexForMainTranslations($languageFilter);
    }

    /**
     * @param \Cabbage\SPI\LanguageFilter $languageFilter
     *
     * @return string[]
     */
    private function resolveWithIndexForMainTranslations(LanguageFilter $languageFilter): array
    {
        $indices = $this->getIndicesByLanguageCodes(
            $languageFilter->getPrioritizedTranslationsLanguageCodes()
        );

        if (empty($indices) || $languageFilter->useMainTranslationFallback()) {
            $indices[] = $this->configuration->getIndexForMainTranslations();
        }

        return $indices;
    }

    /**
     * @param \Cabbage\SPI\LanguageFilter $languageFilter
     *
     * @return string[]
     */
    private function resolveWithoutIndexForMainTranslations(LanguageFilter $languageFilter): array
    {
        if ($languageFilter->useMainTranslationFallback() || !$languageFilter->hasPrioritizedTranslationsLanguageCodes()) {
            return $this->configuration->getAllIndices();
        }

        return $this->getIndicesByLanguageCodes(
            $languageFilter->getPrioritizedTranslationsLanguageCodes()
        );
    }

    /**
     * @param string[] $languageCodes
     *
     * @return string[]
     */
    private function getIndicesByLanguageCodes(array $languageCodes): array
    {
        $indices = [];

        foreach ($languageCodes as $languageCode) {
            $indices[] = $this->getIndexForLanguage($languageCode);
        }

        return $indices;
    }

    private function getIndexForLanguage(string $languageCode): string
    {
        if ($this->configuration->hasIndexForLanguage($languageCode)) {
            return $this->configuration->getIndexForLanguage($languageCode);
        }

        if ($this->configuration->hasDefaultIndex()) {
            return $this->configuration->getDefaultIndex();
        }

        throw new RuntimeException(
            "No index is configured for language code '{$languageCode}'"
        );
    }
}
