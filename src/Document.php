<?php

declare(strict_types=1);

namespace Cabbage;

final class Document
{
    /**
     * @var string
     */
    public $type;

    /**
     * @var array
     */
    public $content;

    /**
     * @param string $type
     * @param array $content
     */
    public function __construct(string $type, array $content)
    {
        $this->type = $type;
        $this->content = $content;
    }
}
