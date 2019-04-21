<?php

declare(strict_types=1);

namespace Cabbage\Core;

use Cabbage\SPI\Index;
use RuntimeException;

/**
 * Represents Elasticsearch cluster configuration for eZ Platform Repository.
 */
final class Cluster
{
    /**
     * @var \Cabbage\SPI\Node[]
     */
    private $coordinatingNodes = [];

    /**
     * @var \Cabbage\SPI\Index[]
     */
    private $indexByLanguageCode;

    /**
     * @var ?\Cabbage\SPI\Index
     */
    private $indexForMainLanguages;

    /**
     * @var ?\Cabbage\SPI\Index
     */
    private $defaultIndex;

    /**
     * @param \Cabbage\SPI\Node[] $coordinatingNodes
     * @param \Cabbage\SPI\Index[] $indexByLanguageCode
     * @param \Cabbage\SPI\Index $indexForMainLanguages
     * @param \Cabbage\SPI\Index $defaultIndex
     */
    public function __construct(
        array $coordinatingNodes,
        array $indexByLanguageCode,
        ?Index $indexForMainLanguages,
        ?Index $defaultIndex
    ) {
        $this->coordinatingNodes = $coordinatingNodes;
        $this->defaultIndex = $defaultIndex;
        $this->indexForMainLanguages = $indexForMainLanguages;
        $this->indexByLanguageCode = $indexByLanguageCode;
    }

    public function getDefaultIndex(): Index
    {
        if ($this->defaultIndex instanceof Index) {
            return $this->defaultIndex;
        }

        throw new RuntimeException(
            'Default index is not defined'
        );
    }

    public function getIndexForMainLanguages(): Index
    {
        if ($this->indexForMainLanguages instanceof Index) {
            return $this->indexForMainLanguages;
        }

        return $this->getDefaultIndex();
    }

    public function getIndexForLanguage(string $languageCode): Index
    {
        if (array_key_exists($languageCode, $this->indexByLanguageCode)) {
            return $this->indexByLanguageCode[$languageCode];
        }

        return $this->getDefaultIndex();
    }

    public function getCoordinatingNodes(): array
    {
        return $this->coordinatingNodes;
    }
}
