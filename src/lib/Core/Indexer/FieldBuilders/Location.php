<?php

declare(strict_types=1);

namespace Cabbage\Core\Indexer\FieldBuilders;

use eZ\Publish\SPI\Persistence\Content;
use eZ\Publish\SPI\Persistence\Content\Location as SPILocation;
use eZ\Publish\SPI\Persistence\Content\Type;

/**
 * Base class for building Fields for Location Documents.
 */
abstract class Location
{
    abstract public function accept(SPILocation $location, Content $content, Type $type): bool;

    /**
     * @param \eZ\Publish\SPI\Persistence\Content\Location $location
     * @param \eZ\Publish\SPI\Persistence\Content $content
     * @param \eZ\Publish\SPI\Persistence\Content\Type $type
     *
     * @return \Cabbage\SPI\Document\Field[]
     */
    abstract public function build(SPILocation $location, Content $content, Type $type): array;
}
