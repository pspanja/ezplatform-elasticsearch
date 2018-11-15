<?php

declare(strict_types=1);

namespace Cabbage\SPI;

/**
 * Defines access to an Elasticsearch index.
 */
final class Index
{
    /**
     * @var \Cabbage\SPI\Node
     */
    public $node;

    /**
     * @var string
     */
    public $index;

    /**
     * @param \Cabbage\SPI\Node $node
     * @param string $index
     */
    public function __construct(Node $node, string $index)
    {
        $this->node = $node;
        $this->index = $index;
    }

    /**
     * Return the URL of the Index instance.
     *
     * @return string
     */
    public function getUrl(): string
    {
        $nodeUrl = $this->node->getUrl();

        return "{$nodeUrl}/{{$this->index}";
    }
}
