<?php

declare(strict_types=1);

namespace Cabbage\Core\Indexer\FieldBuilders\Content;

use Cabbage\Core\Indexer\DocumentBuilder;
use Cabbage\Core\Indexer\FieldBuilders\Content;
use Cabbage\SPI\Document\Field;
use Cabbage\SPI\Document\Field\Type\Identifier;
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
        return [
            new Field(
                'type',
                DocumentBuilder::TypeContent,
                new Identifier()
            )
        ];
    }
}
