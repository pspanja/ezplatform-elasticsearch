<?php

declare(strict_types=1);

namespace Cabbage\Core\Indexer\FieldBuilders;

use eZ\Publish\SPI\Persistence\Content as SPIContent;
use eZ\Publish\SPI\Persistence\Content\Location as SPILocation;
use eZ\Publish\SPI\Persistence\Content\Type;

/**
 * Base class for building Fields for Location Documents in a specific translation.
 */
abstract class TranslationLocation
{
    /**
     * @param string $languageCode
     * @param \eZ\Publish\SPI\Persistence\Content\Location $location
     * @param \eZ\Publish\SPI\Persistence\Content $content
     * @param \eZ\Publish\SPI\Persistence\Content\Type $type
     *
     * @return bool
     */
    abstract public function accept(string $languageCode, SPILocation $location, SPIContent $content, Type $type): bool;

    /**
     * @param string $languageCode
     * @param \eZ\Publish\SPI\Persistence\Content\Location $location
     * @param \eZ\Publish\SPI\Persistence\Content $content
     * @param \eZ\Publish\SPI\Persistence\Content\Type $type
     *
     * @return \Cabbage\SPI\Document\Field[]
     */
    abstract public function build(string $languageCode, SPILocation $location, SPIContent $content, Type $type): array;
}
