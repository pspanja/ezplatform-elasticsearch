<?php

declare(strict_types=1);

namespace Cabbage\Http;

/**
 * Simple HTTP client based on PHP stream.
 */
final class Client
{
    /**
     * HTTP GET method.
     */
    private const GET = 'GET';

    /**
     * HTTP PUT method.
     */
    private const PUT = 'PUT';

    /**
     * Send $request with GET method and return the response.
     *
     * @param \Cabbage\Http\Request $request
     *
     * @return \Cabbage\Http\Response
     */
    public function get(Request $request): Response
    {
        return $this->send($request, self::GET);
    }

    /**
     * Send $request with PUT method and return the response.
     *
     * @param \Cabbage\Http\Request $request
     *
     * @return \Cabbage\Http\Response
     */
    public function put(Request $request): Response
    {
        return $this->send($request, self::PUT);
    }

    /**
     * Send $request with $method and return the response.
     *
     * @param \Cabbage\Http\Request $request
     * @param string $method
     *
     * @return \Cabbage\Http\Response
     */
    private function send(Request $request, string $method): Response
    {
        $context = stream_context_create($this->getStreamContextOptions($request, $method));

        $level = error_reporting(0);
        $body = file_get_contents($request->uri, false, $context);

        error_reporting($level);

        if ($body === false) {
            $error = error_get_last();

            throw new ConnectionException($error['message']);
        }

        return $this->buildResponse($http_response_header, $body);
    }

    /**
     * @param string[] $responseHeaders
     * @param string $body
     *
     * @return \Cabbage\Http\Response
     */
    private function buildResponse(array $responseHeaders, string $body): Response
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

        return new Response($version, $status, $body, $headers);
    }

    /**
     * @param \Cabbage\Http\Request $request
     * @param string $method
     *
     * @return array[]|array[][]
     */
    private function getStreamContextOptions(Request $request, string $method): array
    {
        return [
            'http' => [
                'content' => $request->body,
                'header' => $this->formatHeaders($request),
                'ignore_errors' => true,
                'method' => $method,
            ],
        ];
    }

    /**
     * @param \Cabbage\Http\Request $request
     *
     * @return string[]
     */
    private function formatHeaders(Request $request): array
    {
        $headers = [];

        foreach ($request->headers as $key => $value) {
            $headers[] = "{$key}: $value";
        }

        return $headers;
    }
}
