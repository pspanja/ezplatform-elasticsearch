<?php

declare(strict_types=1);

namespace Cabbage\Tests\Unit;

use Cabbage\Core\IndexRegistry;
use Cabbage\SPI\Index;
use Cabbage\SPI\Node;
use OutOfBoundsException;
use PHPUnit\Framework\TestCase;

class IndexRegistryTest extends TestCase
{
    /**
     * @testdox Index can be registered
     */
    public function testRegistryCanRegister(): void
    {
        $registry = $this->getRegistryUnderTest();

        $index = new Index(Node::fromDsn('http://localhost:9200'), 'a');
        $registry->register('index_a', $index);

        $this->addToAssertionCount(1);
    }

    /**
     * @testdox Index can be retrieved after registering
     * @depends testRegistryCanRegister
     */
    public function testRegistryCanRetrieve(): void
    {
        $registry = $this->getRegistryUnderTest();

        $index = new Index(Node::fromDsn('http://localhost:9200'), 'a');
        $registry->register('index_a', $index);
        $index = $registry->get('index_a');

        $this->assertEquals('a', $index->name);
    }

    /**
     * @testdox Registering second Index with the same name will overwrite the first one
     * @depends testRegistryCanRetrieve
     */
    public function testRegistryCanOverwrite(): void
    {
        $registry = $this->getRegistryUnderTest();

        $index = new Index(Node::fromDsn('http://localhost:9200'), 'a');
        $registry->register('index_a', $index);
        $index = new Index(Node::fromDsn('http://localhost:9200'), 'b');
        $registry->register('index_a', $index);

        $index = $registry->get('index_a');

        $this->assertEquals('b', $index->name);
    }

    /**
     * @testdox Registry will crash if Index is not found
     */
    public function testRegistryCanCrash(): void
    {
        $this->expectException(OutOfBoundsException::class);

        $registry = $this->getRegistryUnderTest();

        $registry->get('index_c');
    }

    /**
     * @var \Cabbage\Core\IndexRegistry
     */
    private $registry;

    protected function getRegistryUnderTest(): IndexRegistry
    {
        if (!isset($this->registry)) {
            $this->registry = new IndexRegistry();
        }

        return $this->registry;
    }
}
