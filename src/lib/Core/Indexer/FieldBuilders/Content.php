<?php

declare(strict_types=1);

namespace Cabbage\Core\Indexer\FieldBuilders;

use eZ\Publish\SPI\Persistence\Content as SPIContent;
use eZ\Publish\SPI\Persistence\Content\Type;

/**
 * Base class for building Fields for Content Documents.
 */
abstract class Content
{
    abstract public function accept(SPIContent $content, Type $type, array $locations): bool;

    /**
     * @param \Cabbage\Core\Indexer\FieldBuilders\Content $content
     * @param \eZ\Publish\SPI\Persistence\Content\Type $type
     * @param \eZ\Publish\SPI\Persistence\Content\Location[] $locations
     *
     * @return \Cabbage\SPI\Document\Field[]
     */
    abstract public function build(SPIContent $content, Type $type, array $locations): array;
}
