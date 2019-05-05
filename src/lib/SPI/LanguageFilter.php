<?php

declare(strict_types=1);

namespace Cabbage\SPI;

final class LanguageFilter
{
    /**
     * @var string[]
     */
    private $prioritizedTranslationLanguageCodes;

    /**
     * @var bool
     */
    private $useMainTranslationFallback;

    /**
     * @param string[] $prioritizedTranslationLanguageCodes
     * @param bool $useMainTranslationFallback
     */
    public function __construct(array $prioritizedTranslationLanguageCodes, bool $useMainTranslationFallback)
    {
        $this->prioritizedTranslationLanguageCodes = $prioritizedTranslationLanguageCodes;
        $this->useMainTranslationFallback = $useMainTranslationFallback;
    }

    public function getPrioritizedTranslationLanguageCodes(): array
    {
        return $this->prioritizedTranslationLanguageCodes;
    }

    public function useMainTranslationFallback(): bool
    {
        return $this->useMainTranslationFallback;
    }
}
