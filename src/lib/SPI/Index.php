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
     * Name of the index.
     *
     * @var string
     */
    public $name;

    /**
     * @param \Cabbage\SPI\Node $node
     * @param string $name
     */
    public function __construct(Node $node, string $name)
    {
        $this->node = $node;
        $this->name = $name;
    }

    /**
     * Return the URL of the Index instance.
     *
     * @return string
     */
    public function getUrl(): string
    {
        $nodeUrl = $this->node->getUrl();

        return "{$nodeUrl}/{$this->name}";
    }
}
