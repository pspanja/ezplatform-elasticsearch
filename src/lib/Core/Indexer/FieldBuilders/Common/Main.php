<?php

declare(strict_types=1);

namespace Cabbage\Core\Indexer\FieldBuilders\Common;

use Cabbage\Core\Indexer\FieldBuilders\Common;
use Cabbage\SPI\Document\Field;
use Cabbage\SPI\Document\Field\Type\Identifier;
use eZ\Publish\SPI\Persistence\Content as SPIContent;
use eZ\Publish\SPI\Persistence\Content\Type;

final class Main extends Common
{
    public function accept(SPIContent $content, Type $type, array $locations): bool
    {
        return true;
    }

    public function build(SPIContent $content, Type $type, array $locations): array
    {
        return [
            new Field(
                'content_id',
                $content->versionInfo->contentInfo->id,
                new Identifier()
            ),
        ];
    }
}
