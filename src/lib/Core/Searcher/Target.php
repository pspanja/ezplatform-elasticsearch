<?php

declare(strict_types=1);

namespace Cabbage\Core\Searcher;

use function array_map;
use Cabbage\SPI\Index;
use InvalidArgumentException;

/**
 * Represents a Query target, consisting of a coordinating Node and an array of Indices.
 *
 * @see \eZ\Publish\API\Repository\Values\Content\Query
 * @see \Cabbage\SPI\Node
 * @see \Cabbage\SPI\Index
 */
final class Target
{
    /**
     * @var \Cabbage\SPI\Index[]
     */
    private $indices;

    /**
     * @param \Cabbage\SPI\Index[] $indices
     */
    public function __construct(array $indices)
    {
        if (empty($indices)) {
            throw new InvalidArgumentException(
                'Argument $indices must not be empty'
            );
        }

        $this->indices = $indices;
    }

    public function getIndices(): string
    {
        $indexNames = array_map(
            static function (Index $index): string {return $index->name;},
            $this->indices
        );

        return implode(',', $indexNames);
    }
}
