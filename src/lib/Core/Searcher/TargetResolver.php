<?php

declare(strict_types=1);

namespace Cabbage\Core\Searcher;

use Cabbage\Core\Cluster;
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
     * @var \Cabbage\Core\Cluster
     */
    private $cluster;

    public function __construct(Cluster $cluster)
    {
        $this->cluster = $cluster;
    }

    public function resolve(LanguageFilter $languageFilter): Target
    {
        $indices = $this->resolveIndices($languageFilter);

        if (empty($indices)) {
            throw new RuntimeException(
                'No indices were resolved for the given LanguageFilter'
            );
        }

        return new Target($indices[0]->node, $indices);
    }

    /**
     * @param \Cabbage\SPI\LanguageFilter $languageFilter
     *
     * @return \Cabbage\SPI\Index[]
     */
    private function resolveIndices(LanguageFilter $languageFilter): array
    {
        if ($this->cluster->hasIndexForMainTranslations()) {
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
            $indices[] = $this->cluster->getIndexForMainTranslations();
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
            $indices = $this->cluster->getIndicesForAllLanguages();

            if ($this->cluster->hasDefaultIndex()) {
                $indices[] = $this->cluster->getDefaultIndex();
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
        if ($this->cluster->hasIndexForLanguage($languageCode)) {
            return $this->cluster->getIndexForLanguage($languageCode);
        }

        if ($this->cluster->hasDefaultIndex()) {
            return $this->cluster->getDefaultIndex();
        }

        throw new RuntimeException(
            "Couldn't resolve index for language code '{$languageCode}'"
        );
    }
}
