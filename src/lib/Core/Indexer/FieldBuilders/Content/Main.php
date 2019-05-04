<?php

declare(strict_types=1);

namespace Cabbage\Core\Indexer\FieldBuilders\Content;

use Cabbage\Core\Indexer\FieldBuilders\Content;
use eZ\Publish\SPI\Persistence\Content as SPIContent;
use eZ\Publish\SPI\Persistence\Content\Type;

final class Main extends Content
{
    public function accept(SPIContent $content, Type $type, array $locations): bool
    {
        return true;
    }

    public function build(SPIContent $content, Type $type, array $locations): array
    {
        return [];
    }
}
