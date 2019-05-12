<?php

declare(strict_types=1);

namespace Cabbage\Core\HttpClient;

use RuntimeException;

/**
 * Thrown when the HTTP client can't connect to the server.
 */
final class ConnectionException extends RuntimeException
{
    /**
     * @param string $url
     * @param string $error
     */
    public function __construct(string $url, string $error)
    {
        parent::__construct('Could not connect to "' . $url . '": ' . $error);
    }
}
