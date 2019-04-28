<?php

declare(strict_types=1);

namespace Cabbage\Core\Indexer\FieldBuilders\Location;

use Cabbage\Core\Indexer\FieldBuilders\Location;
use eZ\Publish\SPI\Persistence\Content;
use eZ\Publish\SPI\Persistence\Content\Location as SPILocation;
use eZ\Publish\SPI\Persistence\Content\Type;

/**
 * Abstract implementation serves as an extension point for custom builders.
 */
final class Aggregate extends Location
{
    /**
     * @var \Cabbage\Core\Indexer\FieldBuilders\Location[]
     */
    private $builders = [];

    /**
     * @param \Cabbage\Core\Indexer\FieldBuilders\Location[] $builders
     */
    public function __construct(array $builders)
    {
        foreach ($builders as $builder) {
            $this->addBuilder($builder);
        }
    }

    public function addBuilder(Location $builder): void
    {
        $this->builders[] = $builder;
    }

    public function accept(SPILocation $location, Content $content, Type $type): bool
    {
        return true;
    }

    public function build(SPILocation $location, Content $content, Type $type): array
    {
        $fieldsGrouped = [[]];

        foreach ($this->builders as $builder) {
            if ($builder->accept($location, $content, $type)) {
                $fieldsGrouped[] = $builder->build($location, $content, $type);
            }
        }

        return array_merge(...$fieldsGrouped);
    }
}
