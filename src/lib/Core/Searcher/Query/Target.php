<?php

declare(strict_types=1);

namespace Cabbage\Core\Searcher\Query;

use function array_map;
use Cabbage\SPI\Index;
use Cabbage\SPI\Node;
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
     * @var array|\Cabbage\SPI\Node
     */
    private $coordinatingNode;

    /**
     * @var array|\Cabbage\SPI\Index[]
     */
    private $indices;

    /**
     * @param \Cabbage\SPI\Node $coordinatingNode
     * @param \Cabbage\SPI\Index[] $indices
     */
    public function __construct(Node $coordinatingNode, array $indices)
    {
        if (empty($indices)) {
            throw new InvalidArgumentException(
                'Argument $indices must not be empty'
            );
        }

        $this->coordinatingNode = $coordinatingNode;
        $this->indices = $indices;
    }

    public function getUrl(): string
    {
        $nodeUrl = $this->coordinatingNode->getUrl();
        $indexNames = array_map(static function (Index $index) {return $index->name;}, $this->indices);
        $indexNames = implode(',', $indexNames);

        return "{$nodeUrl}/{$indexNames}/_search";
    }
}
