<?php

declare(strict_types=1);

namespace Cabbage\Tests\Integration;

use Cabbage\Endpoint;

class ConfiguratorTest extends BaseTest
{
    /**
     * @var \Cabbage\Endpoint
     */
    private static $endpoint;

    /**
     * @var \Cabbage\Configurator
     */
    private static $configurator;

    /**
     * @throws \Exception
     */
    public static function setUpBeforeClass(): void
    {
        self::$endpoint = Endpoint::fromDsn('http://localhost:9200/configurator_test');
        self::$configurator = self::getContainer()->get('cabbage.configurator');

        if (self::$configurator->hasIndex(self::$endpoint)) {
            self::$configurator->deleteIndex(self::$endpoint);
        }
    }

    public function testDoesNotHaveIndexUntilCreated(): void
    {
        $this->assertFalse(self::$configurator->hasIndex(self::$endpoint));
    }

    /**
     * @depends testDoesNotHaveIndexUntilCreated
     */
    public function testCreateIndex(): void
    {
        $response = self::$configurator->createIndex(self::$endpoint);

        $this->assertEquals(200, $response->status);
    }

    /**
     * @depends testCreateIndex
     */
    public function testHasIndexAfterCreated(): void
    {
        $this->assertTrue(self::$configurator->hasIndex(self::$endpoint));
    }

    /**
     * @depends testHasIndexAfterCreated
     */
    public function  testDeleteIndex(): void
    {
        $response = self::$configurator->deleteIndex(self::$endpoint);

        $this->assertEquals(200, $response->status);
    }

    /**
     * @depends testDeleteIndex
     */
    public function  testDoesNotHaveIndexAfterDeleted(): void
    {
        $this->assertFalse(self::$configurator->hasIndex(self::$endpoint));
    }
}
