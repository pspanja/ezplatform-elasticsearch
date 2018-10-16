<?php

declare(strict_types=1);

namespace Cabbage\Core\Http;

/**
 * Represents a HTTP response.
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
     * Response body.
     *
     * @var string
     */
    public $body;

    /**
     * Response headers.
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

    /**
     * Build an instance of Response from headers and body.
     *
     * @param string[] $responseHeaders
     * @param string $body
     *
     * @return \Cabbage\Core\Http\Response
     */
    public static function fromHeadersAndBody(array $responseHeaders, string $body): self
    {
        $status = 200;
        $version = '1.1';
        $headers = [];
        $pattern = '(^HTTP/(?P<version>\\d+\\.\\d+)\\s+(?P<status>\\d+))S';

        foreach ($responseHeaders as $responseHeader) {
            if (preg_match($pattern, $responseHeader, $matches)) {
                $version = $matches['version'];
                $status = (int)$matches['status'];
                $headers = [];
            } else {
                [$key, $value] = explode(':', $responseHeader, 2);
                $headers[$key] = trim($value);
            }
        }

        return new self($version, $status, $body, $headers);
    }
}
