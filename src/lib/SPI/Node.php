<?php

declare(strict_types=1);

namespace Cabbage\SPI;

use RuntimeException;

/**
 * Defines access to an Elasticsearch node.
 */
final class Node
{
    /**
     * @var string
     */
    public $scheme;

    /**
     * @var string
     */
    public $host;

    /**
     * @var int
     */
    public $port;

    /**
     * Default properties for the Node.
     *
     * @var array
     */
    static private $defaults = [
        'scheme' => 'http',
        'port' => 9200,
    ];

    /**
     * @param string $scheme
     * @param string $host
     * @param int $port
     */
    public function __construct(string $scheme, string $host, int $port)
    {
        $this->scheme = $scheme;
        $this->host = $host;
        $this->port = $port;
    }

    /**
     * Return the URL of the Node instance.
     *
     * @return string
     */
    public function getUrl(): string
    {
        return "{$this->scheme}://{$this->host}:{$this->port}";
    }

    /**
     * Build the Node instance from the given $dsn.
     *
     * Valid DSN is in the form of 'scheme://host:port'.
     *
     * @param string $dsn
     *
     * @return \Cabbage\SPI\Node
     */
    public static function fromDsn(string $dsn): self
    {
        $elements = self::parseDsn($dsn);

        return new self(
            $elements['scheme'],
            $elements['host'],
            $elements['port']
        );
    }

    /**
     * @param string $dsn
     *
     * @return mixed[]
     */
    private static function parseDsn(string $dsn): array
    {
        $elements = parse_url(rtrim($dsn, '/'));

        if ($elements === false) {
            throw new RuntimeException('Failed to parse the given DSN');
        }

        self::validate($elements);

        return $elements + self::$defaults;
    }

    private static function validate(array $elements): void
    {
        if (array_key_exists('user', $elements) || array_key_exists('pass', $elements)) {
            throw new RuntimeException(
                "DSN does not support the 'user' and 'pass' components"
            );
        }

        if (array_key_exists('path', $elements)) {
            throw new RuntimeException("DSN does not support the 'path' component");
        }

        if (array_key_exists('query', $elements)) {
            throw new RuntimeException("DSN does not support the 'query' component");
        }

        if (array_key_exists('fragment', $elements)) {
            throw new RuntimeException("DSN does not support the 'fragment' component");
        }
    }
}
