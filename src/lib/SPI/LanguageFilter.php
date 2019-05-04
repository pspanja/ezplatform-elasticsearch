<?php

declare(strict_types=1);

namespace Cabbage\SPI;

final class LanguageFilter
{
    /**
     * @var string[]
     */
    private $prioritizedLanguageCodeList;

    /**
     * @var bool
     */
    private $useMainTranslationFallback;

    /**
     * @param array $prioritizedLanguageCodeList
     * @param bool $useMainTranslationFallback
     */
    public function __construct(array $prioritizedLanguageCodeList, bool $useMainTranslationFallback)
    {
        $this->prioritizedLanguageCodeList = $prioritizedLanguageCodeList;
        $this->useMainTranslationFallback = $useMainTranslationFallback;
    }

    public function getPrioritizedLanguageCodeList(): array
    {
        return $this->prioritizedLanguageCodeList;
    }

    public function useMainTranslationFallback(): bool
    {
        return $this->useMainTranslationFallback;
    }
}
