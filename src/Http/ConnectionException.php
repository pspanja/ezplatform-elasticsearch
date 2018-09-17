<?php declare(strict_types=1);

namespace Cabbage\Http;

use RuntimeException;

/**
 * HTTP client connection exception.
 */
final class ConnectionException extends RuntimeException
{
    public function __construct(string $server)
    {
        parent::__construct("Could not connect to server '{$server}'");
    }
}
