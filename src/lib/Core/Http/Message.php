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
     * @param string $string
     *
     * @return \Cabbage\Core\Http\Message
     */
    public static function fromString(string $string): self
    {
        return new self($string, ['Content-Type' => 'application/json']);
    }

    /**
     * Build the Message instance from the given hash array.
     *
     * @param array $hash
     *
     * @return \Cabbage\Core\Http\Message
     */
    public static function fromHash(array $hash): self
    {
        return static::fromString(json_encode($hash, JSON_THROW_ON_ERROR));
    }
}
