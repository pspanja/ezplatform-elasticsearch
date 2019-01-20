<?php

declare(strict_types=1);

namespace Cabbage\Tests\Integration\Core;

use Cabbage\API\Query\Criterion\DocumentType;
use Cabbage\Core\Handler;
use Cabbage\SPI\Document;
use Cabbage\SPI\Index;
use Cabbage\SPI\Node;
use eZ\Publish\API\Repository\Values\Content\LocationQuery;
use eZ\Publish\API\Repository\Values\Content\Query;
use eZ\Publish\SPI\Persistence\Content;
use eZ\Publish\SPI\Persistence\Content\ContentInfo;
use eZ\Publish\SPI\Persistence\Content\Location;
use eZ\Publish\SPI\Persistence\Content\VersionInfo;

class HandlerTest extends BaseTest
{
    /**
     * @var \Cabbage\SPI\Index
     */
    private static $index;

    /**
     * @throws \Exception
     */
    public static function setUpBeforeClass(): void
    {
        $node = Node::fromDsn('http://localhost:9200');
        self::$index = new Index($node, 'index');
        $configurator = self::getContainer()->get('cabbage.configurator');

        if ($configurator->hasIndex(self::$index)) {
            $configurator->deleteIndex(self::$index);
        }

        $mapping = \file_get_contents(__DIR__ . '/../../../config/elasticsearch/mapping.json');

        $configurator->createIndex(self::$index);
        $configurator->setMapping(self::$index, $mapping);
    }

    /**
     * @testdox Content can be sent to server for indexing
     *
     * @throws \Exception
     */
    public function testIndexContent(): void
    {
        $handler = $this->getHandlerUnderTest();
        $content = new Content([
            'versionInfo' => new VersionInfo([
                'contentInfo' => new ContentInfo([
                    'id' => 'CONTENT_ID',
                ]),
            ]),
            'fields' => [],
        ]);

        $handler->indexContent($content);
        $this->flush(self::$index);

        $this->addToAssertionCount(1);
    }

    /**
     * @testdox Indexed Content can be found
     * @depends testIndexContent
     *
     * @throws \Exception
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     */
    public function testFindContent(): void
    {
        $handler = $this->getHandlerUnderTest();
        $query = new Query([
            'filter' => new DocumentType(Document::TypeContent),
        ]);

        $searchResult = $handler->findContent($query);

        $this->assertEquals(1, $searchResult->totalCount);
        $this->assertInstanceOf(ContentInfo::class, $searchResult->searchHits[0]->valueObject);
        $this->assertEquals(
            'CONTENT_ID',
            $searchResult->searchHits[0]->valueObject->id
        );
    }

    /**
     * @testdox Indexed Locations can be found
     * @depends testIndexContent
     *
     * @throws \Exception
     */
    public function testFindLocation(): void
    {
        $handler = $this->getHandlerUnderTest();
        $query = new LocationQuery([
            'filter' => new DocumentType(Document::TypeLocation),
        ]);

        $searchResult = $handler->findLocations($query);

        $this->assertEquals(1, $searchResult->totalCount);
        $this->assertInstanceOf(Location::class, $searchResult->searchHits[0]->valueObject);
        $this->assertEquals(
            'LOCATION_ID',
            $searchResult->searchHits[0]->valueObject->id
        );
    }

    /**
     * @testdox Index can be purged
     *
     * @throws \Exception
     */
    public function testPurgeIndex(): void
    {
        $handler = $this->getHandlerUnderTest();

        $handler->purgeIndex();
        $this->flush(self::$index);

        $query = new Query([
            'filter' => new DocumentType(Document::TypeContent),
        ]);

        $searchResult = $handler->findContent($query);
        $this->assertEquals(0, $searchResult->totalCount);

        $query = new LocationQuery([
            'filter' => new DocumentType(Document::TypeLocation),
        ]);

        $searchResult = $handler->findLocations($query);
        $this->assertEquals(0, $searchResult->totalCount);
    }

    /**
     * @throws \Exception
     *
     * @return \Cabbage\Core\Handler
     */
    protected function getHandlerUnderTest(): Handler
    {
        return self::getContainer()->get('cabbage.handler');
    }

    /**
     * @throws \Exception
     *
     * @param \Cabbage\SPI\Index $index
     */
    protected function flush(Index $index): void
    {
        self::getContainer()->get('cabbage.gateway')->flush($index);
    }
}
