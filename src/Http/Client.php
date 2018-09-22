<?php

declare(strict_types=1);

namespace Cabbage\Http;

/**
 * Simple HTTP client based on PHP stream.
 */
final class Client
{
    /**
     * Send $request and return the response.
     *
     * @param \Cabbage\Http\Request $request
     *
     * @return \Cabbage\Http\Response
     */
    public function send(Request $request): Response
    {
        $context = stream_context_create($this->getStreamContextOptions($request));

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
     *
     * @return array[]|array[][]
     */
    private function getStreamContextOptions(Request $request): array
    {
        return [
            'http' => [
                'content' => $request->body,
                'header' => $this->formatHeaders($request),
                'ignore_errors' => true,
                'method' => $request->method,
            ],
        ];
    }

    private function formatHeaders(Request $request): array
    {
        $headers = [];

        foreach ($request->headers as $key => $value) {
            $headers[] = "{$key}: $value";
        }

        return $headers;
    }
}
