<?php

declare(strict_types=1);

namespace Cabbage\Core\Searcher;

use Cabbage\Core\Cluster\Configuration;
use Cabbage\SPI\TranslationFilter;
use RuntimeException;

/**
 * Matches a TranslationFilter to an array of indices.
 *
 * @see \Cabbage\SPI\TranslationFilter
 */
final class TranslationFilterIndicesResolver
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
     * @param \Cabbage\SPI\TranslationFilter $translationFilter
     *
     * @return string[]
     */
    public function resolve(TranslationFilter $translationFilter): array
    {
        $indices = $this->resolveIndices($translationFilter);

        if (empty($indices)) {
            throw new RuntimeException(
                'No indices are configured for the given TranslationFilter'
            );
        }

        return $indices;
    }

    /**
     * @param \Cabbage\SPI\TranslationFilter $translationFilter
     *
     * @return string[]
     */
    private function resolveIndices(TranslationFilter $translationFilter): array
    {
        if ($this->configuration->hasIndexForMainTranslations()) {
            return $this->resolveWithIndexForMainTranslations($translationFilter);
        }

        return $this->resolveWithoutIndexForMainTranslations($translationFilter);
    }

    /**
     * @param \Cabbage\SPI\TranslationFilter $translationFilter
     *
     * @return string[]
     */
    private function resolveWithIndexForMainTranslations(TranslationFilter $translationFilter): array
    {
        $indices = $this->getIndicesByLanguageCodes(
            $translationFilter->getPrioritizedTranslationsLanguageCodes()
        );

        if (empty($indices) || $translationFilter->useMainTranslationFallback()) {
            $indices[] = $this->configuration->getIndexForMainTranslations();
        }

        return $indices;
    }

    /**
     * @param \Cabbage\SPI\TranslationFilter $translationFilter
     *
     * @return string[]
     */
    private function resolveWithoutIndexForMainTranslations(TranslationFilter $translationFilter): array
    {
        if ($translationFilter->useMainTranslationFallback() || !$translationFilter->hasPrioritizedTranslationsLanguageCodes()) {
            return $this->configuration->getAllIndices();
        }

        return $this->getIndicesByLanguageCodes(
            $translationFilter->getPrioritizedTranslationsLanguageCodes()
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
