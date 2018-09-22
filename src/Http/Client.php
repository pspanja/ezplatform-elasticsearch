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
     * @param string $uri
     *
     * @return \Cabbage\Http\Response
     */
    public function get(Request $request, string $uri): Response
    {
        return $this->send($request, $uri, self::GET);
    }

    /**
     * Send $request with PUT method and return the response.
     *
     * @param \Cabbage\Http\Request $request
     * @param string $uri
     *
     * @return \Cabbage\Http\Response
     */
    public function put(Request $request, string $uri): Response
    {
        return $this->send($request, $uri, self::PUT);
    }

    /**
     * Send $request with $method and return the response.
     *
     * @param \Cabbage\Http\Request $request
     * @param string $uri
     * @param string $method
     *
     * @return \Cabbage\Http\Response
     */
    private function send(Request $request, string $uri, string $method): Response
    {
        $context = stream_context_create($this->getStreamContextOptions($request, $method));

        $level = error_reporting(0);
        $body = file_get_contents($uri, false, $context);

        error_reporting($level);

        if ($body === false) {
            $error = error_get_last();

            throw new ConnectionException($error['message']);
        }

        return Response::fromHeadersAndBody($http_response_header, $body);
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
