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
     * @param \Cabbage\Http\Message $message
     * @param string $url
     *
     * @return \Cabbage\Http\Response
     */
    public function get(Message $message, string $url): Response
    {
        return $this->send($message, $url, self::GET);
    }

    /**
     * Send $message to $url with PUT method and return the response.
     *
     * @param \Cabbage\Http\Message $message
     * @param string $url
     *
     * @return \Cabbage\Http\Response
     */
    public function put(Message $message, string $url): Response
    {
        return $this->send($message, $url, self::PUT);
    }

    /**
     * Send $message to $url with POST method and return the response.
     *
     * @param \Cabbage\Http\Message $message
     * @param string $url
     *
     * @return \Cabbage\Http\Response
     */
    public function post(Message $message, string $url): Response
    {
        return $this->send($message, $url, self::POST);
    }

    /**
     * Send $message to $url with DELETE method and return the response.
     *
     * @param \Cabbage\Http\Message $message
     * @param string $url
     *
     * @return \Cabbage\Http\Response
     */
    public function delete(Message $message, string $url): Response
    {
        return $this->send($message, $url, self::DELETE);
    }

    /**
     * Send $message to $url with HEAD method and return the response.
     *
     * @param \Cabbage\Http\Message $message
     * @param string $url
     *
     * @return \Cabbage\Http\Response
     */
    public function head(Message $message, string $url): Response
    {
        return $this->send($message, $url, self::HEAD);
    }

    /**
     * Send $message with $method and return the response.
     *
     * @param \Cabbage\Http\Message $message
     * @param string $url
     * @param string $method
     *
     * @return \Cabbage\Http\Response
     */
    private function send(Message $message, string $url, string $method): Response
    {
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
