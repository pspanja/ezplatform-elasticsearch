<?php

declare(strict_types=1);

namespace Cabbage\Core\Indexer\FieldBuilders\Location;

use Cabbage\Core\Indexer\DocumentBuilder;
use Cabbage\Core\Indexer\FieldBuilders\Location;
use Cabbage\SPI\Document\Field;
use Cabbage\SPI\Document\Field\Type\Identifier;
use eZ\Publish\SPI\Persistence\Content as SPIContent;
use eZ\Publish\SPI\Persistence\Content\Location as SPILocation;
use eZ\Publish\SPI\Persistence\Content\Type;

final class Main extends Location
{
    public function accept(SPILocation $location, SPIContent $content, Type $type): bool
    {
        return true;
    }

    public function build(SPILocation $location, SPIContent $content, Type $type): array
    {
        return [
            new Field(
                'type',
                DocumentBuilder::TypeLocation,
                new Identifier()
            ),
            new Field(
                'location_id',
                $location->id,
                new Identifier()
            ),
        ];
    }
}
