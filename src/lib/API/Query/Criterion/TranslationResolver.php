<?php

declare(strict_types=1);

namespace Cabbage\API\Query\Criterion;

use Cabbage\SPI\TranslationFilter;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion;

/**
 * Defines a match on document's translation.
 *
 * @see \Cabbage\SPI\TranslationFilter
 */
final class TranslationResolver extends Criterion
{
    /**
     * @var \Cabbage\SPI\TranslationFilter
     */
    public $translationFilter;

    /**
     * @param \Cabbage\SPI\TranslationFilter $translationFilter
     */
    public function __construct(TranslationFilter $translationFilter)
    {
        $this->translationFilter = $translationFilter;
    }

    /**
     * @return \eZ\Publish\API\Repository\Values\Content\Query\Criterion\Operator\Specifications[]
     */
    public function getSpecifications(): array
    {
        return [];
    }
}
