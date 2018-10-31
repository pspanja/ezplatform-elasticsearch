<?php

declare(strict_types=1);

namespace Cabbage\Core\Http;

/**
 * Allows communication with Elasticsearch server.
 *
 * This HTTP client is based on PHP stream.
 */
final class Client
{
    /**
     * HTTP GET method.
     */
    private const GET = 'GET';

    /**
     * HTTP HEAD method.
     */
    private const HEAD = 'HEAD';

    /**
     * HTTP PUT method.
     */
    private const POST = 'POST';

    /**
     * HTTP PUT method.
     */
    private const PUT = 'PUT';

    /**
     * HTTP DELETE method.
     */
    private const DELETE = 'DELETE';

    /**
     * Perform GET request to the URL with the given message.
     *
     * @param string $url
     * @param \Cabbage\Core\Http\Message $message
     *
     * @return \Cabbage\Core\Http\Response
     */
    public function get(string $url, ?Message $message = null): Response
    {
        return $this->request(self::GET, $url, $message);
    }

    /**
     * Perform PUT request to the URL with the given message.
     *
     * @param string $url
     * @param \Cabbage\Core\Http\Message $message
     *
     * @return \Cabbage\Core\Http\Response
     */
    public function put(string $url, ?Message $message = null): Response
    {
        return $this->request(self::PUT, $url, $message);
    }

    /**
     * Perform POST request to the URL with the given message.
     *
     * @param string $url
     * @param \Cabbage\Core\Http\Message $message
     *
     * @return \Cabbage\Core\Http\Response
     */
    public function post(string $url, ?Message $message = null): Response
    {
        return $this->request(self::POST, $url, $message);
    }

    /**
     * Perform DELETE request to the URL with the given message.
     *
     * @param string $url
     * @param \Cabbage\Core\Http\Message $message
     *
     * @return \Cabbage\Core\Http\Response
     */
    public function delete(string $url, ?Message $message = null): Response
    {
        return $this->request(self::DELETE, $url, $message);
    }

    /**
     * Perform HEAD request to the URL with the given message.
     *
     * @param string $url
     * @param \Cabbage\Core\Http\Message $message
     *
     * @return \Cabbage\Core\Http\Response
     */
    public function head(string $url, ?Message $message = null): Response
    {
        return $this->request(self::HEAD, $url, $message);
    }

    /**
     * Send $message with $method and return the response.
     *
     * @param string $method
     * @param string $url
     * @param \Cabbage\Core\Http\Message|null $message
     *
     * @return \Cabbage\Core\Http\Response
     */
    private function request(string $method, string $url, ?Message $message = null): Response
    {
        $message = $message ?? new Message();
        $context = stream_context_create($this->getStreamContextOptions($message, $method));

        $level = error_reporting(0);
        $body = file_get_contents($url, false, $context);

        error_reporting($level);

        if ($body === false) {
            $error = error_get_last();

            throw new ConnectionException($error['message']);
        }

        return Response::fromHeadersAndBody($http_response_header, $body);
    }

    /**
     * @param \Cabbage\Core\Http\Message $message
     * @param string $method
     *
     * @return array[]|array[][]
     */
    private function getStreamContextOptions(Message $message, string $method): array
    {
        return [
            'http' => [
                'content' => $message->body,
                'header' => $this->formatHeaders($message),
                'ignore_errors' => true,
                'method' => $method,
            ],
        ];
    }

    /**
     * @param \Cabbage\Core\Http\Message $message
     *
     * @return string[]
     */
    private function formatHeaders(Message $message): array
    {
        $headers = [];

        foreach ($message->headers as $key => $value) {
            $headers[] = "{$key}: $value";
        }

        return $headers;
    }
}
