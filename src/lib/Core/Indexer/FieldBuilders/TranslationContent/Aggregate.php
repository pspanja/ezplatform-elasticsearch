<?php

declare(strict_types=1);

namespace Cabbage\Core\Indexer\FieldBuilders\TranslationContent;

use Cabbage\Core\Indexer\FieldBuilders\TranslationContent;
use eZ\Publish\SPI\Persistence\Content;
use eZ\Publish\SPI\Persistence\Content\Type;

/**
 * Abstract implementation serves as an extension point for custom builders.
 */
final class Aggregate extends TranslationContent
{
    /**
     * @var \Cabbage\Core\Indexer\FieldBuilders\TranslationContent[]
     */
    private $builders = [];

    /**
     * @param \Cabbage\Core\Indexer\FieldBuilders\TranslationContent[] $builders
     */
    public function __construct(array $builders)
    {
        foreach ($builders as $builder) {
            $this->addBuilder($builder);
        }
    }

    public function addBuilder(TranslationContent $builder): void
    {
        $this->builders[] = $builder;
    }

    public function accept(Content $content, Type $type): bool
    {
        return true;
    }

    public function build(Content $content, Type $type): array
    {
        $fieldsGrouped = [[]];

        foreach ($this->builders as $builder) {
            if ($builder->accept($content, $type)) {
                $fieldsGrouped[] = $builder->build($content, $type);
            }
        }

        return array_merge(...$fieldsGrouped);
    }
}
