<?php

declare(strict_types=1);

namespace Cabbage\Tests\Integration\Http;

use Cabbage\Http\Client;
use Cabbage\Http\ConnectionException;
use Cabbage\Http\Request;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class ClientTest extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        $timeout = (int)getenv('serverTimeout');
        $host = (string)getenv('serverHost');
        $port = (int)getenv('serverPort');
        $documentRoot = (string)getenv('serverDocumentRoot');

        static::startServer($host, $port, $documentRoot);
        static::waitForServer($host, $port, $timeout);
    }

    public function testSendRequestFound(): void
    {
        $client = $this->getClientUnderTest();

        $host = getenv('serverHost');
        $port = (int)getenv('serverPort');

        $request = new Request("http://{$host}:{$port}/something.txt");
        $response = $client->get($request);

        $this->assertEquals(200, $response->status);
        $this->assertEquals("something in a text file\n", $response->body);
    }

    public function testSendRequestNotFound(): void
    {
        $client = $this->getClientUnderTest();

        $host = getenv('serverHost');
        $port = (int)getenv('serverPort');

        $request = new Request("http://{$host}:{$port}/not_found.mpg");
        $response = $client->get($request);

        $this->assertEquals(404, $response->status);
    }

    public function testSendRequestThrowsConnectionException(): void
    {
        $client = $this->getClientUnderTest();

        $this->expectException(ConnectionException::class);

        $request = new Request('http://remotehost:12345/passwords.txt');
        $client->get($request);
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
