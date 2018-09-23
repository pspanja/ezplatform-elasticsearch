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
     * @var mixed
     */
    public $content;

    /**
     * @param string $type
     * @param mixed $content
     */
    public function __construct(string $type, $content)
    {
        $this->type = $type;
        $this->content = $content;
    }
}
