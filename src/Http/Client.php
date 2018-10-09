<?php

declare(strict_types=1);

namespace Cabbage\Http;

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
     * Send $message to $url with GET method and return the response.
     *
     * @param string $url
     * @param \Cabbage\Http\Message $message
     *
     * @return \Cabbage\Http\Response
     */
    public function get(string $url, ?Message $message = null): Response
    {
        return $this->send($url, self::GET, $message);
    }

    /**
     * Send $message to $url with PUT method and return the response.
     *
     * @param string $url
     * @param \Cabbage\Http\Message $message
     *
     * @return \Cabbage\Http\Response
     */
    public function put(string $url, ?Message $message = null): Response
    {
        return $this->send($url, self::PUT, $message);
    }

    /**
     * Send $message to $url with POST method and return the response.
     *
     * @param string $url
     * @param \Cabbage\Http\Message $message
     *
     * @return \Cabbage\Http\Response
     */
    public function post(string $url, ?Message $message = null): Response
    {
        return $this->send($url, self::POST, $message);
    }

    /**
     * Send $message to $url with DELETE method and return the response.
     *
     * @param string $url
     * @param \Cabbage\Http\Message $message
     *
     * @return \Cabbage\Http\Response
     */
    public function delete(string $url, ?Message $message = null): Response
    {
        return $this->send($url, self::DELETE, $message);
    }

    /**
     * Send $message to $url with HEAD method and return the response.
     *
     * @param string $url
     * @param \Cabbage\Http\Message $message
     *
     * @return \Cabbage\Http\Response
     */
    public function head(string $url, ?Message $message = null): Response
    {
        return $this->send($url, self::HEAD, $message);
    }

    /**
     * Send $message with $method and return the response.
     *
     * @param string $url
     * @param string $method
     * @param \Cabbage\Http\Message $message
     *
     * @return \Cabbage\Http\Response
     */
    private function send(string $url, string $method, ?Message $message = null): Response
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
     * @param \Cabbage\Http\Message $message
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
     * @param \Cabbage\Http\Message $message
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
