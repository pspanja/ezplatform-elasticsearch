<?php

declare(strict_types=1);

namespace Cabbage\Http;

/**
 * HTTP response.
 */
final class Response
{
    /**
     * HTTP version.
     *
     * @var string
     */
    public $version;

    /**
     * HTTP response status code.
     *
     * @var int
     */
    public $status;

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
     * @param int $status
     * @param string $version
     */
    public function __construct(string $version, int $status, string $body, array $headers)
    {
        $this->version = $version;
        $this->status = $status;
        $this->body = $body;
        $this->headers = $headers;
    }
}
