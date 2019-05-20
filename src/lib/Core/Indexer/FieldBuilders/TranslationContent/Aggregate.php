<?php

declare(strict_types=1);

namespace Cabbage\Core\Indexer\FieldBuilders\TranslationContent;

use Cabbage\Core\Indexer\FieldBuilders\TranslationContent;
use eZ\Publish\SPI\Persistence\Content as SPIContent;
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

    public function accept(string $languageCode, SPIContent $content, Type $type, array $locations): bool
    {
        return true;
    }

    public function build(string $languageCode, SPIContent $content, Type $type, array $locations): array
    {
        $fieldsGrouped = [[]];

        foreach ($this->builders as $builder) {
            if ($builder->accept($languageCode, $content, $type, $locations)) {
                $fieldsGrouped[] = $builder->build($languageCode, $content, $type, $locations);
            }
        }

        return array_merge(...$fieldsGrouped);
    }
}
