<?php

declare(strict_types=1);

namespace Cabbage\Tests\Integration\Core;

use Cabbage\SPI\Index;
use Cabbage\SPI\Node;
use function file_get_contents;

class ConfiguratorTest extends BaseTest
{
    /**
     * @var \Cabbage\SPI\Index
     */
    private static $index;

    /**
     * @var \Cabbage\Core\Configurator
     */
    private static $configurator;

    /**
     * @throws \Exception
     */
    public static function setUpBeforeClass(): void
    {
        $node = Node::fromDsn('http://localhost:9200');
        self::$index = new Index($node, 'configurator_test');
        self::$configurator = self::getContainer()->get('cabbage.configurator');

        if (self::$configurator->hasIndex(self::$index)) {
            self::$configurator->deleteIndex(self::$index);
        }
    }

    /**
     * @testdox Index initially does not exist
     */
    public function testDoesNotHaveIndexUntilCreated(): void
    {
        $this->assertFalse(self::$configurator->hasIndex(self::$index));
    }

    /**
     * @testdox Index can be created
     * @depends testDoesNotHaveIndexUntilCreated
     */
    public function testCreateIndex(): void
    {
        $response = self::$configurator->createIndex(self::$index);

        $this->assertEquals(200, $response->status);
    }

    /**
     * @testdox Index exists after it's created
     * @depends testCreateIndex
     */
    public function testHasIndexAfterCreated(): void
    {
        $this->assertTrue(self::$configurator->hasIndex(self::$index));
    }

    /**
     * @testdox Mapping can be set to index
     * @depends testHasIndexAfterCreated
     */
    public function testSetMapping(): void
    {
        $mapping = file_get_contents(__DIR__ . '/../../../config/elasticsearch/mapping.json');

        $response = self::$configurator->setMapping(self::$index, $mapping);

        $this->assertEquals(200, $response->status);
    }

    /**
     * @testdox Index can be deleted
     * @depends testHasIndexAfterCreated
     */
    public function testDeleteIndex(): void
    {
        $response = self::$configurator->deleteIndex(self::$index);

        $this->assertEquals(200, $response->status);
    }

    /**
     * @testdox Index doesn't exist after it's deleted
     * @depends testDeleteIndex
     */
    public function testDoesNotHaveIndexAfterDeleted(): void
    {
        $this->assertFalse(self::$configurator->hasIndex(self::$index));
    }
}
