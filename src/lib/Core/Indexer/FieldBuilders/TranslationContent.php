<?php

declare(strict_types=1);

namespace Cabbage\Core\Indexer\FieldBuilders;

use eZ\Publish\SPI\Persistence\Content;
use eZ\Publish\SPI\Persistence\Content\Type;

/**
 * Base class for building Fields for Content Documents in a specific translation.
 */
abstract class TranslationContent
{
    /**
     * @param \eZ\Publish\SPI\Persistence\Content $content
     * @param \eZ\Publish\SPI\Persistence\Content\Type $type
     *
     * @return bool
     */
    abstract public function accept(Content $content, Type $type): bool;

    /**
     * @param \eZ\Publish\SPI\Persistence\Content $content
     * @param \eZ\Publish\SPI\Persistence\Content\Type $type
     *
     * @return \Cabbage\SPI\Document\Field[]
     */
    abstract public function build(Content $content, Type $type): array;
}
