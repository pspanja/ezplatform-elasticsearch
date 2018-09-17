<?php

declare(strict_types=1);

namespace Cabbage\Http;

/**
 * HTTP request.
 */
final class Request
{
    /**
     * HTTP GET method.
     */
    public const GET = 'GET';

    /**
     * HTTP URI.
     *
     * @var string
     */
    public $uri;

    /**
     * HTTP method.
     *
     * @var string
     */
    public $method;

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
     * @param string $method
     * @param string[] $headers
     * @param string $body
     */
    public function __construct(
        string $uri,
        string $method = self::GET,
        string $body = '',
        array $headers = []
    ) {
        $this->uri = $uri;
        $this->method = $method;
        $this->headers = $headers;
        $this->body = $body;
    }
}
