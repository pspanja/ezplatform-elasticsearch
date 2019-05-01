<?php

declare(strict_types=1);

namespace Cabbage\Core\Indexer\FieldBuilders;

use eZ\Publish\SPI\Persistence\Content;
use eZ\Publish\SPI\Persistence\Content\Type;

/**
 * Base class for building Fields for both Content and Location Documents.
 */
abstract class Common
{
    abstract public function accept(Content $content, Type $type, array $locations): bool;

    /**
     * @param \eZ\Publish\SPI\Persistence\Content $content
     * @param \eZ\Publish\SPI\Persistence\Content\Type $type
     * @param \eZ\Publish\SPI\Persistence\Content\Location[] $locations
     *
     * @return \Cabbage\SPI\Document\Field[]
     */
    abstract public function build(Content $content, Type $type, array $locations): array;
}
