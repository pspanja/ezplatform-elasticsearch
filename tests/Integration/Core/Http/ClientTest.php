<?php

declare(strict_types=1);

namespace Cabbage\Tests\Integration\Core\Http;

use Cabbage\Core\Http\Client;
use Cabbage\Core\Http\ConnectionException;
use Cabbage\Core\Http\Message;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class ClientTest extends TestCase
{
    /**
     * @var int
     */
    protected static $timeout = 5;

    /**
     * @var string
     */
    protected static $host = 'localhost';

    /**
     * @var int
     */
    protected static $port = 8005;

    /**
     * @var string
     */
    protected static $documentRoot = __DIR__ . '/_fixtures/';

    public static function setUpBeforeClass(): void
    {
        static::startServer(self::$host, self::$port, self::$documentRoot);
        static::waitForServer(self::$host, self::$port, self::$timeout);
    }

    /**
     * @testdox GET request to known resource returns response with status 200
     */
    public function testGetRequestFound(): void
    {
        $client = $this->getClientUnderTest();

        $host = self::$host;
        $port = self::$port;

        $message = new Message();
        $response = $client->get("http://{$host}:{$port}/something.txt", $message);

        $this->assertEquals(200, $response->status);
        $this->assertEquals("something in a text file\n", $response->body);
    }

    /**
     * @testdox GET request to unknown resource returns response with status 400
     */
    public function testGetRequestNotFound(): void
    {
        $client = $this->getClientUnderTest();

        $host = self::$host;
        $port = self::$port;

        $message = new Message();
        $response = $client->get("http://{$host}:{$port}/not_found.mpg", $message);

        $this->assertEquals(404, $response->status);
    }

    /**
     * @testdox ConnectionException is thrown when host does not exist
     */
    public function testGetRequestThrowsConnectionException(): void
    {
        $client = $this->getClientUnderTest();

        $this->expectException(ConnectionException::class);

        $message = new Message();
        $client->get('http://remotehost:12345/passwords.txt', $message);
    }

    protected function getClientUnderTest(): Client
    {
        return new Client();
    }

    /**
     * @param string $host
     * @param int $port
     * @param int $timeout
     */
    protected static function waitForServer(string $host, int $port, int $timeout): void
    {
        $start = microtime(true);
        $reachable = false;

        while (microtime(true) - $start <= $timeout) {
            if (self::isServerReachable($host, $port)) {
                $reachable = true;

                break;
            }

            usleep(10000);
        }

        if (!$reachable) {
            throw new RuntimeException('Could not connect to the web server');
        }
    }

    /**
     * @param string $host
     * @param int $port
     *
     * @return bool
     */
    protected static function isServerReachable(string $host, int $port): bool
    {
        set_error_handler(function () {return true; });
        $pointer = fsockopen($host, $port);
        restore_error_handler();

        if ($pointer === false) {
            return false;
        }

        fclose($pointer);

        return true;
    }

    /**
     * @param string $host
     * @param int $port
     * @param string $documentRoot
     */
    protected static function startServer(string $host, int $port, string $documentRoot): void
    {
        $command = sprintf(
            'php -S %s:%d -t %s >/dev/null 2>&1 & echo $!',
            $host,
            $port,
            $documentRoot
        );

        $output = [];
        exec($command, $output);
        $pid = (int)$output[0];

        if (!$pid) {
            throw new RuntimeException('Could not start the web server');
        }

        register_shutdown_function(
            function () use ($pid): void {
                echo "Killing HTTP server process #{$pid}\n";
                exec("kill {$pid}");
            }
        );
    }
}
