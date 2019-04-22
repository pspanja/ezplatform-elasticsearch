<?php

declare(strict_types=1);

namespace Cabbage\Core\Searcher;

final class LanguageFilter
{
    /**
     * @var string[]
     */
    private $prioritizedLanguageCodeList;

    /**
     * @var bool
     */
    private $useMainLanguageFallback;

    /**
     * @param array $prioritizedLanguageCodeList
     * @param bool $useMainLanguageFallback
     */
    public function __construct(array $prioritizedLanguageCodeList, bool $useMainLanguageFallback)
    {
        $this->prioritizedLanguageCodeList = $prioritizedLanguageCodeList;
        $this->useMainLanguageFallback = $useMainLanguageFallback;
    }

    public function getPrioritizedLanguageCodeList(): array
    {
        return $this->prioritizedLanguageCodeList;
    }

    public function useMainLanguageFallback(): bool
    {
        return $this->useMainLanguageFallback;
    }
}
