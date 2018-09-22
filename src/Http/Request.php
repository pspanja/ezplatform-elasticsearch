<?php

declare(strict_types=1);

namespace Cabbage\Http;

/**
 * HTTP request.
 */
final class Request
{
    /**
     * HTTP URI.
     *
     * @var string
     */
    public $uri;

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
     * Construct from headers and body.
     *
     * @param string $uri
     * @param string[] $headers
     * @param string $body
     */
    public function __construct(
        string $uri,
        string $body = '',
        array $headers = []
    ) {
        $this->uri = $uri;
        $this->headers = $headers;
        $this->body = $body;
    }
}
