<?php

declare(strict_types=1);

namespace Cabbage\Core\Indexer\FieldBuilders;

use eZ\Publish\SPI\Persistence\Content;
use eZ\Publish\SPI\Persistence\Content\Location;
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
    abstract public function accept(string $languageCode, Location $location, Content $content, Type $type): bool;

    /**
     * @param string $languageCode
     * @param \eZ\Publish\SPI\Persistence\Content\Location $location
     * @param \eZ\Publish\SPI\Persistence\Content $content
     * @param \eZ\Publish\SPI\Persistence\Content\Type $type
     *
     * @return \Cabbage\SPI\Document\Field[]
     */
    abstract public function build(string $languageCode, Location $location, Content $content, Type $type): array;
}
