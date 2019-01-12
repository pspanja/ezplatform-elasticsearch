<?php

declare(strict_types=1);

namespace Cabbage\Core\Document\Serializer\FieldValueMapper\Visitor;

use Cabbage\Core\Document\Serializer\FieldValueMapper\Visitor;
use Cabbage\SPI\Document\Field;
use Cabbage\SPI\Document\Field\Type\Boolean as BooleanType;

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
