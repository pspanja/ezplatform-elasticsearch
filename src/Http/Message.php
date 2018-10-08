<?php

declare(strict_types=1);

namespace Cabbage\Http;

/**
 * Represents a HTTP request message.
 */
final class Message
{
    /**
     * Message body.
     *
     * @var string
     */
    public $body;

    /**
     * Message headers.
     *
     * @var string[]
     */
    public $headers;

    /**
     * @param string[] $headers
     * @param string $body
     */
    public function __construct(string $body = '', array $headers = [])
    {
        $this->headers = $headers;
        $this->body = $body;
    }

    /**
     * Build the Message instance from the given JSON string.
     *
     * @param string $json
     *
     * @return \Cabbage\Http\Message
     */
    public static function fromJson(string $json): self
    {
        return new self($json, ['Content-Type' => 'application/json']);
    }
}
