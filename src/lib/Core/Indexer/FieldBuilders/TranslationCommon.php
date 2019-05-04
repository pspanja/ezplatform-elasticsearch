<?php

declare(strict_types=1);

namespace Cabbage\Core\Indexer\FieldBuilders;

use eZ\Publish\SPI\Persistence\Content as SPIContent;
use eZ\Publish\SPI\Persistence\Content\Type;

/**
 * Base class for building Fields for both Content and Location Documents in a specific translation.
 */
abstract class TranslationCommon
{
    /**
     * @param string $languageCode
     * @param \eZ\Publish\SPI\Persistence\Content $content
     * @param \eZ\Publish\SPI\Persistence\Content\Type $type
     * @param \eZ\Publish\SPI\Persistence\Content\Location[] $locations
     *
     * @return bool
     */
    abstract public function accept(string $languageCode, SPIContent $content, Type $type, array $locations): bool;

    /**
     * @param string $languageCode
     * @param \eZ\Publish\SPI\Persistence\Content $content
     * @param \eZ\Publish\SPI\Persistence\Content\Type $type
     * @param \eZ\Publish\SPI\Persistence\Content\Location[] $locations
     *
     * @return \Cabbage\SPI\Document\Field[]
     */
    abstract public function build(string $languageCode, SPIContent $content, Type $type, array $locations): array;
}
