<?php

declare(strict_types=1);

namespace Cabbage\Core\Cluster;

use Cabbage\Core\Searcher\Target;
use Cabbage\SPI\Index;
use Cabbage\SPI\LanguageFilter;
use RuntimeException;

/**
 * Matches a LanguageFilter to a Target.
 *
 * @see \Cabbage\Core\Searcher\Target
 * @see \Cabbage\SPI\LanguageFilter
 */
final class TargetResolver
{
    /**
     * @var \Cabbage\Core\Cluster\Configuration
     */
    private $configuration;

    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;
    }

    public function resolve(LanguageFilter $languageFilter): Target
    {
        $indices = $this->resolveIndices($languageFilter);

        if (empty($indices)) {
            throw new RuntimeException(
                'No indices could be resolved for the given LanguageFilter'
            );
        }

        return new Target($indices);
    }

    /**
     * @param \Cabbage\SPI\LanguageFilter $languageFilter
     *
     * @return \Cabbage\SPI\Index[]
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
     * @return \Cabbage\SPI\Index[]
     */
    private function resolveWithIndexForMainTranslations(LanguageFilter $languageFilter): array
    {
        $indices = $this->getIndicesByPrioritizedTranslationLanguageCodes($languageFilter);

        if (empty($indices) || $languageFilter->useMainTranslationFallback()) {
            $indices[] = $this->configuration->getIndexForMainTranslations();
        }

        return $indices;
    }

    /**
     * @param \Cabbage\SPI\LanguageFilter $languageFilter
     *
     * @return \Cabbage\SPI\Index[]
     */
    private function resolveWithoutIndexForMainTranslations(LanguageFilter $languageFilter): array
    {
        if ($languageFilter->useMainTranslationFallback() || !$languageFilter->hasPrioritizedTranslationLanguageCodes()) {
            $indices = $this->configuration->getIndicesForAllLanguages();

            if ($this->configuration->hasDefaultIndex()) {
                $indices[] = $this->configuration->getDefaultIndex();
            }

            return $indices;
        }

        return $this->getIndicesByPrioritizedTranslationLanguageCodes($languageFilter);
    }

    /**
     * @param \Cabbage\SPI\LanguageFilter $languageFilter
     *
     * @return \Cabbage\SPI\Index[]
     */
    private function getIndicesByPrioritizedTranslationLanguageCodes(LanguageFilter $languageFilter): array
    {
        $indices = [];

        foreach ($languageFilter->getPrioritizedTranslationLanguageCodes() as $languageCode) {
            $indices[] = $this->getIndexForLanguage($languageCode);
        }

        return $indices;
    }

    private function getIndexForLanguage(string $languageCode): Index
    {
        if ($this->configuration->hasIndexForLanguage($languageCode)) {
            return $this->configuration->getIndexForLanguage($languageCode);
        }

        if ($this->configuration->hasDefaultIndex()) {
            return $this->configuration->getDefaultIndex();
        }

        throw new RuntimeException(
            "Couldn't resolve index for language code '{$languageCode}'"
        );
    }
}
