<?php

declare(strict_types=1);

namespace Cabbage;

use RuntimeException;

/**
 * Defines access to an Elasticsearch index.
 */
final class Endpoint
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
     * @var string
     */
    public $index;

    /**
     * @param string $scheme
     * @param string $host
     * @param int $port
     * @param string $index
     */
    public function __construct(string $scheme, string $host, int $port, string $index)
    {
        $this->scheme = $scheme;
        $this->host = $host;
        $this->port = $port;
        $this->index = $index;
    }

    /**
     * Return the URL of the Endpoint instance.
     *
     * @return string
     */
    public function getUrl(): string
    {
        return "{$this->scheme}://{$this->host}:{$this->port}/{$this->index}";
    }

    /**
     * Build the Endpoint instance from the given $dsn.
     *
     * Valid DSN is in the form of 'scheme://host:port/index'.
     *
     * @param string $dsn
     *
     * @return \Cabbage\Endpoint
     */
    public static function fromDsn(string $dsn): self
    {
        $elements = self::parseDsn($dsn);

        return new self(
            $elements['scheme'],
            $elements['host'],
            $elements['port'],
            $elements['path']
        );
    }

    /**
     * @param string $dsn
     *
     * @return array
     */
    private static function parseDsn(string $dsn): array
    {
        $defaults = [
            'scheme' => 'http',
            'port' => 9200,
        ];
        $elements = parse_url(rtrim($dsn, '/'));

        if ($elements === false) {
            throw new RuntimeException(
                'Failed to parse the given DSN'
            );
        }

        $elements += $defaults;
        $elements['path'] = trim($elements['path'], '/');
        self::validateIndex($elements['path']);

        return $elements;
    }

    /**
     * @param string $index
     */
    private static function validateIndex(string $index): void
    {
        if (\is_string($index) && \strpos($index, '/') !== false) {
            throw new RuntimeException(
                'Index name must not contain a slash'
            );
        }
    }
}
