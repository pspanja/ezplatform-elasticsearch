<?php

declare(strict_types=1);

namespace Cabbage\Core\Indexer\FieldBuilders\Common;

use Cabbage\Core\Indexer\FieldBuilders\Common;
use eZ\Publish\SPI\Persistence\Content;
use eZ\Publish\SPI\Persistence\Content\Type;

/**
 * Abstract implementation serves as an extension point for custom builders.
 */
final class Aggregate extends Common
{
    /**
     * @var \Cabbage\Core\Indexer\FieldBuilders\Content[]
     */
    private $builders = [];

    /**
     * @param \Cabbage\Core\Indexer\FieldBuilders\Content[] $builders
     */
    public function __construct(array $builders)
    {
        foreach ($builders as $builder) {
            $this->addBuilder($builder);
        }
    }

    public function addBuilder(Common $builder): void
    {
        $this->builders[] = $builder;
    }

    public function accept(Content $content, Type $type, array $locations): bool
    {
        return true;
    }

    public function build(Content $content, Type $type, array $locations): array
    {
        $fieldsGrouped = [[]];

        foreach ($this->builders as $builder) {
            if ($builder->accept($content, $type, $locations)) {
                $fieldsGrouped[] = $builder->build($content, $type, $locations);
            }
        }

        return array_merge(...$fieldsGrouped);
    }
}