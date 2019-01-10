<?php

declare(strict_types=1);

namespace Cabbage\Core\Http;

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
     * @param string $body
     * @param string[] $headers
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
     * @return \Cabbage\Core\Http\Message
     */
    public static function fromJson(string $json): self
    {
        return new self($json, ['Content-Type' => 'application/json']);
    }

    /**
     * Build the Message instance from the given hash array.
     *
     * @param array $hash
     *
     * @return \Cabbage\Core\Http\Message
     */
    public static function fromJsonHash(array $hash): self
    {
        return static::fromJson(json_encode($hash, JSON_THROW_ON_ERROR));
    }
}
