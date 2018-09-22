<?php

declare(strict_types=1);

namespace Cabbage\Http;

/**
 * HTTP request.
 */
final class Request
{
    /**
     * Request/response body.
     *
     * @var string
     */
    public $body;

    /**
     * Request/response headers.
     *
     * @var string[]
     */
    public $headers;

    /**
     * @param string[] $headers
     * @param string $body
     */
    public function __construct(string $body = '', array $headers = [])
    {
        $this->headers = $headers;
        $this->body = $body;
    }
}
