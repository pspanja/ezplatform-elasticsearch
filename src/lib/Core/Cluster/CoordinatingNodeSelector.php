<?php

declare(strict_types=1);

namespace Cabbage\Core\Cluster;

use Cabbage\SPI\Node;
use RuntimeException;

/**
 * Selects coordinating node through which Elasticsearch cluster is accessed.
 */
final class CoordinatingNodeSelector
{
    /**
     * @var \Cabbage\Core\Cluster\Configuration
     */
    private $configuration;

    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;
    }

    public function select(): Node
    {
        $coordinatingNodes = $this->configuration->getCoordinatingNodes();

        if (empty($coordinatingNodes)) {
            throw new RuntimeException(
                'No coordinating Nodes are configured'
            );
        }

        return $coordinatingNodes[array_rand($coordinatingNodes, 1)];
    }
}
