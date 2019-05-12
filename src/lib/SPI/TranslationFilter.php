<?php

declare(strict_types=1);

namespace Cabbage\SPI;

final class TranslationFilter
{
    /**
     * @var string[]
     */
    private $prioritizedTranslationsLanguageCodes;

    /**
     * @var bool
     */
    private $useMainTranslationFallback;

    /**
     * @param string[] $prioritizedTranslationsLanguageCodes
     * @param bool $useMainTranslationFallback
     */
    public function __construct(array $prioritizedTranslationsLanguageCodes, bool $useMainTranslationFallback)
    {
        $this->prioritizedTranslationsLanguageCodes = $prioritizedTranslationsLanguageCodes;
        $this->useMainTranslationFallback = $useMainTranslationFallback;
    }

    public function hasPrioritizedTranslationsLanguageCodes(): bool
    {
        return !empty($this->prioritizedTranslationsLanguageCodes);
    }

    public function getPrioritizedTranslationsLanguageCodes(): array
    {
        return $this->prioritizedTranslationsLanguageCodes;
    }

    public function useMainTranslationFallback(): bool
    {
        return $this->useMainTranslationFallback;
    }
}
