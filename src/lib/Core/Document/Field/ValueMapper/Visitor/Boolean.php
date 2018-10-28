<?php

declare(strict_types=1);

namespace Cabbage\Core\Document\Field\ValueMapper\Visitor;

use Cabbage\Core\Document\Field\ValueMapper\Visitor;
use Cabbage\SPI\Field;
use Cabbage\SPI\FieldType\Boolean as BooleanType;

class Boolean extends Visitor
{
    public function accept(Field $field): bool
    {
        return $field->type instanceof BooleanType;
    }

    public function visit(Field $field)
    {
        return (bool)$field->value;
    }
}
