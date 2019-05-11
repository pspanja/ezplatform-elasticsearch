<?php

declare(strict_types=1);

namespace Cabbage\Core;

use Cabbage\Core\Cluster\CoordinatingNodeSelector;
use Cabbage\SPI\Node;

/**
 * Represents Elasticsearch cluster configuration for eZ Platform Repository.
 */
final class Cluster
{
    /**
     * @var \Cabbage\Core\Cluster\CoordinatingNodeSelector
     */
    private $coordinatingNodeSelector;

    /**
     * @param \Cabbage\Core\Cluster\CoordinatingNodeSelector $coordinatingNodeSelector
     */
    public function __construct(CoordinatingNodeSelector $coordinatingNodeSelector)
    {
        $this->coordinatingNodeSelector = $coordinatingNodeSelector;
    }

    public function selectCoordinatingNode(): Node
    {
        return $this->coordinatingNodeSelector->select();
    }
}
