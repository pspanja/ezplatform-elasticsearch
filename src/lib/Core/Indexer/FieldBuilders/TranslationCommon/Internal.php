<?php

declare(strict_types=1);

namespace Cabbage\Core\Indexer\FieldBuilders\TranslationCommon;

use Cabbage\Core\Indexer\FieldBuilders\TranslationCommon;
use Cabbage\SPI\Document\Field;
use Cabbage\SPI\Document\Field\Type\Boolean;
use eZ\Publish\SPI\Persistence\Content as SPIContent;
use eZ\Publish\SPI\Persistence\Content\Type;

final class Internal extends TranslationCommon
{
    public function accept(string $languageCode, SPIContent $content, Type $type, array $locations): bool
    {
        return true;
    }

    public function build(string $languageCode, SPIContent $content, Type $type, array $locations): array
    {
        $contentInfo = $content->versionInfo->contentInfo;
        $isMainTranslation = $languageCode === $contentInfo->mainLanguageCode;

        return [
            new Field(
                '__internal__translation_language_code',
                $languageCode,
                new Boolean()
            ),
            new Field(
                '__internal__translation_is_main_translation',
                $isMainTranslation,
                new Boolean()
            ),
            new Field(
                '__internal__translation_is_used_for_fallback',
                $isMainTranslation && $contentInfo->alwaysAvailable,
                new Boolean()
            ),
        ];
    }
}
