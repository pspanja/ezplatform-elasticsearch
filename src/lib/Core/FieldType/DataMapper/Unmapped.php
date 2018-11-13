<?php

declare(strict_types=1);

namespace Cabbage\Core\FieldType\DataMapper;

use Cabbage\SPI\FieldType\DataMapper;
use eZ\Publish\SPI\Persistence\Content\Field;
use eZ\Publish\SPI\Persistence\Content\Type\FieldDefinition;

final class Unmapped extends DataMapper
{
    public function map(Field $field, FieldDefinition $fieldDefinition): array
    {
        return [];
    }
}
