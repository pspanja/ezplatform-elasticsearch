<?php

declare(strict_types=1);

namespace Cabbage\SPI;

use RuntimeException;

abstract class ValueObject
{
    /**
     * Handle reading the value of non-visible property.
     *
     * @param string $name
     *
     * @return mixed
     */
    public function __get(string $name)
    {
        if (property_exists($this, $name)) {
            return $this->{$name};
        }

        $fullName = $this->getFullName($name);

        throw new RuntimeException(
            "Property {$fullName} was not found"
        );
    }

    /**
     * Handle setting the value of non-visible property.
     *
     * @param string $name
     * @param mixed $value
     */
    public function __set(string $name, $value): void
    {
        $fullName = $this->getFullName($name);

        if (property_exists($this, $name)) {
            throw new RuntimeException(
                "Property {$fullName} is read-only"
            );
        }

        throw new RuntimeException(
            "Property {$fullName} was not found"
        );
    }

    /**
     * Handle unsetting the value of non-visible property.
     *
     * @param string $name
     */
    public function __unset(string $name): void
    {
        $this->__set($name, null);
    }

    /**
     * Handle checking if the non-visible property exists.
     *
     * @param string $name
     *
     * @return bool
     */
    public function __isset(string $name): bool
    {
        return property_exists($this, $name);
    }

    private function getFullName(string $name): string
    {
        $class = \get_class($this);

        return "{$class}::\${$name}";
    }
}
