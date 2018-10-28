<?php

declare(strict_types=1);

namespace Cabbage\Core\Document\Field\ValueMapper\Visitor;

use Cabbage\Core\Document\Field\ValueMapper\Visitor;
use Cabbage\SPI\Field;
use Cabbage\SPI\FieldType\Keyword as KeywordType;

class Keyword extends Visitor
{
    public function accept(Field $field): bool
    {
        return $field->type instanceof KeywordType;
    }

    public function visit(Field $field)
    {
        return (string)$field->value;
    }
}
