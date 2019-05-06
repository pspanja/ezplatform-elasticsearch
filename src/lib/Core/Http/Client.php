<?php

declare(strict_types=1);

namespace Cabbage\Core\Http;

/**
 * Provides HTTP communication with Elasticsearch server.
 *
 * This HTTP client is based on PHP stream.
 */
final class Client
{
    /**
     * Perform GET request to the URL with the given message.
     *
     * @param \Cabbage\Core\Http\Message $message
     * @param string $url
     *
     * @return \Cabbage\Core\Http\Response
     */
    public function get(Message $message, string $url): Response
    {
        return $this->send($message, 'GET', $url);
    }

    /**
     * Perform PUT request to the URL with the given message.
     *
     * @param string $url
     * @param \Cabbage\Core\Http\Message $message
     *
     * @return \Cabbage\Core\Http\Response
     */
    public function put(Message $message, string $url): Response
    {
        return $this->send($message, 'PUT', $url);
    }

    /**
     * Perform POST request to the URL with the given message.
     *
     * @param \Cabbage\Core\Http\Message $message
     * @param string $url
     *
     * @return \Cabbage\Core\Http\Response
     */
    public function post(Message $message, string $url): Response
    {
        return $this->send($message, 'POST', $url);
    }

    /**
     * Perform DELETE request to the URL with the given message.
     *
     * @param \Cabbage\Core\Http\Message $message
     * @param string $url
     *
     * @return \Cabbage\Core\Http\Response
     */
    public function delete(Message $message, string $url): Response
    {
        return $this->send($message, 'DELETE', $url);
    }

    /**
     * Perform HEAD request to the URL with the given message.
     *
     * @param \Cabbage\Core\Http\Message $message
     * @param string $url
     *
     * @return \Cabbage\Core\Http\Response
     */
    public function head(Message $message, string $url): Response
    {
        return $this->send($message, 'HEAD', $url);
    }

    /**
     * Send $message with $method and return the response.
     *
     * @param \Cabbage\Core\Http\Message $message
     * @param string $method
     * @param string $url
     *
     * @return \Cabbage\Core\Http\Response
     */
    private function send(Message $message, string $method, string $url): Response
    {
        $context = stream_context_create($this->getStreamContextOptions($message, $method));

        $level = error_reporting(0);
        $body = file_get_contents($url, false, $context);

        error_reporting($level);

        if ($body === false) {
            $error = error_get_last();
            $errorMessage = $error['message'] ?? 'Unknown error';

            throw new ConnectionException($errorMessage);
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
