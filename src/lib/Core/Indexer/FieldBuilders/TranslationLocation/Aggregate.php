<?php

declare(strict_types=1);

namespace Cabbage\Core\Indexer\FieldBuilders\TranslationLocation;

use Cabbage\Core\Indexer\FieldBuilders\TranslationLocation;
use eZ\Publish\SPI\Persistence\Content as SPIContent;
use eZ\Publish\SPI\Persistence\Content\Location as SPILocation;
use eZ\Publish\SPI\Persistence\Content\Type;

/**
 * Abstract implementation serves as an extension point for custom builders.
 */
final class Aggregate extends TranslationLocation
{
    /**
     * @var \Cabbage\Core\Indexer\FieldBuilders\TranslationLocation[]
     */
    private $builders = [];

    /**
     * @param \Cabbage\Core\Indexer\FieldBuilders\TranslationLocation[] $builders
     */
    public function __construct(array $builders)
    {
        foreach ($builders as $builder) {
            $this->addBuilder($builder);
        }
    }

    public function addBuilder(TranslationLocation $builder): void
    {
        $this->builders[] = $builder;
    }

    public function accept(string $languageCode, SPILocation $location, SPIContent $content, Type $type): bool
    {
        return true;
    }

    public function build(string $languageCode, SPILocation $location, SPIContent $content, Type $type): array
    {
        $fieldsGrouped = [[]];

        foreach ($this->builders as $builder) {
            if ($builder->accept($languageCode, $location, $content, $type)) {
                $fieldsGrouped[] = $builder->build($languageCode, $location, $content, $type);
            }
        }

        return array_merge(...$fieldsGrouped);
    }
}
