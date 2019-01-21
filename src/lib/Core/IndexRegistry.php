<?php

declare(strict_types=1);

namespace Cabbage\Core;

use Cabbage\SPI\Index;
use OutOfBoundsException;

final class IndexRegistry
{
    /**
     * @var \Cabbage\SPI\Index[]
     */
    private $indexByIdentifier = [];

    /**
     * @param \Cabbage\SPI\Index[] $indexByIdentifier
     */
    public function __construct(array $indexByIdentifier = [])
    {
        foreach ($indexByIdentifier as $identifier => $index) {
            $this->register($identifier, $index);
        }
    }

    public function register(string $identifier, Index $index): void
    {
        $this->indexByIdentifier[$identifier] = $index;
    }

    public function get(string $identifier): Index
    {
        if (array_key_exists($identifier, $this->indexByIdentifier)) {
            return $this->indexByIdentifier[$identifier];
        }

        throw new OutOfBoundsException(
            "No Index is registered for '{$identifier}' "
        );
    }
}
