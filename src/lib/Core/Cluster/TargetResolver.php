<?php

declare(strict_types=1);

namespace Cabbage\Core\Cluster;

use Cabbage\Core\Cluster;
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
    public function resolve(Cluster $cluster, LanguageFilter $languageFilter): Target
    {
        $indices = $this->resolveIndices($cluster, $languageFilter);

        if (empty($indices)) {
            throw new RuntimeException(
                'No indices could be resolved for the given LanguageFilter'
            );
        }

        return new Target($indices);
    }

    /**
     * @param \Cabbage\Core\Cluster $cluster
     * @param \Cabbage\SPI\LanguageFilter $languageFilter
     *
     * @return \Cabbage\SPI\Index[]
     */
    private function resolveIndices(Cluster $cluster, LanguageFilter $languageFilter): array
    {
        if ($cluster->hasIndexForMainTranslations()) {
            return $this->resolveWithIndexForMainTranslations($cluster, $languageFilter);
        }

        return $this->resolveWithoutIndexForMainTranslations($cluster, $languageFilter);
    }

    /**
     * @param \Cabbage\Core\Cluster $cluster
     * @param \Cabbage\SPI\LanguageFilter $languageFilter
     *
     * @return \Cabbage\SPI\Index[]
     */
    private function resolveWithIndexForMainTranslations(Cluster $cluster, LanguageFilter $languageFilter): array
    {
        $indices = $this->getIndicesByPrioritizedTranslationLanguageCodes($cluster, $languageFilter);

        if (empty($indices) || $languageFilter->useMainTranslationFallback()) {
            $indices[] = $cluster->getIndexForMainTranslations();
        }

        return $indices;
    }

    /**
     * @param \Cabbage\Core\Cluster $cluster
     * @param \Cabbage\SPI\LanguageFilter $languageFilter
     *
     * @return \Cabbage\SPI\Index[]
     */
    private function resolveWithoutIndexForMainTranslations(Cluster $cluster, LanguageFilter $languageFilter): array
    {
        if ($languageFilter->useMainTranslationFallback() || !$languageFilter->hasPrioritizedTranslationLanguageCodes()) {
            $indices = $cluster->getIndicesForAllLanguages();

            if ($cluster->hasDefaultIndex()) {
                $indices[] = $cluster->getDefaultIndex();
            }

            return $indices;
        }

        return $this->getIndicesByPrioritizedTranslationLanguageCodes($cluster, $languageFilter);
    }

    /**
     * @param \Cabbage\Core\Cluster $cluster
     * @param \Cabbage\SPI\LanguageFilter $languageFilter
     *
     * @return \Cabbage\SPI\Index[]
     */
    private function getIndicesByPrioritizedTranslationLanguageCodes(Cluster $cluster, LanguageFilter $languageFilter): array
    {
        $indices = [];

        foreach ($languageFilter->getPrioritizedTranslationLanguageCodes() as $languageCode) {
            $indices[] = $this->getIndexForLanguage($cluster, $languageCode);
        }

        return $indices;
    }

    private function getIndexForLanguage(Cluster $cluster, string $languageCode): Index
    {
        if ($cluster->hasIndexForLanguage($languageCode)) {
            return $cluster->getIndexForLanguage($languageCode);
        }

        if ($cluster->hasDefaultIndex()) {
            return $cluster->getDefaultIndex();
        }

        throw new RuntimeException(
            "Couldn't resolve index for language code '{$languageCode}'"
        );
    }
}
