<?php

declare(strict_types=1);

namespace Cabbage\Tests\Integration\Core;

use Cabbage\API\Query\Criterion\DocumentType;
use Cabbage\Core\Indexer\DocumentBuilder;
use Cabbage\Core\Engine;
use Cabbage\SPI\Node;
use eZ\Publish\API\Repository\Values\Content\LocationQuery;
use eZ\Publish\API\Repository\Values\Content\Query;
use eZ\Publish\SPI\Persistence\Content;
use eZ\Publish\SPI\Persistence\Content\ContentInfo;
use eZ\Publish\SPI\Persistence\Content\Location;
use eZ\Publish\SPI\Persistence\Content\VersionInfo;
use function file_get_contents;

class EngineTest extends BaseTest
{
    /**
     * @var \Cabbage\SPI\Node
     */
    private static $node;

    /**
     * @var string
     */
    private static $index;

    /**
     * @throws \Exception
     */
    public static function setUpBeforeClass(): void
    {
        self::$node = Node::fromDsn('http://localhost:9200');
        self::$index = 'index';
        $configurator = self::getContainer()->get('cabbage.configurator');

        if ($configurator->hasIndex(self::$node, self::$index)) {
            $configurator->deleteIndex(self::$node, self::$index);
        }

        $mapping = file_get_contents(__DIR__ . '/../../../config/elasticsearch/mapping.json');

        $configurator->createIndex(self::$node, self::$index);
        $configurator->setMapping(self::$node, self::$index, $mapping);
    }

    /**
     * @testdox Content can be sent to server for indexing
     *
     * @throws \Exception
     */
    public function testIndexContent(): void
    {
        $engine = $this->getEngineUnderTest();
        $content = new Content([
            'versionInfo' => new VersionInfo([
                'contentInfo' => new ContentInfo([
                    'id' => 'CONTENT_ID',
                    'mainLanguageCode' => 'cro-HR',
                    'alwaysAvailable' => true,
                ]),
                'languageCodes' => ['cro-HR'],
            ]),
            'fields' => [],
        ]);

        $engine->indexContent($content);
        $this->refresh(self::$node);

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
        $engine = $this->getEngineUnderTest();
        $query = new Query([
            'filter' => new DocumentType(DocumentBuilder::TypeContent),
        ]);

        $searchResult = $engine->findContent($query);

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
        $engine = $this->getEngineUnderTest();
        $query = new LocationQuery([
            'filter' => new DocumentType(DocumentBuilder::TypeLocation),
        ]);

        $searchResult = $engine->findLocations($query);

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
        $engine = $this->getEngineUnderTest();

        $engine->purgeIndex();
        $this->refresh(self::$node);

        $query = new Query([
            'filter' => new DocumentType(DocumentBuilder::TypeContent),
        ]);

        $searchResult = $engine->findContent($query);
        $this->assertEquals(0, $searchResult->totalCount);

        $query = new LocationQuery([
            'filter' => new DocumentType(DocumentBuilder::TypeLocation),
        ]);

        $searchResult = $engine->findLocations($query);
        $this->assertEquals(0, $searchResult->totalCount);
    }

    /**
     * @throws \Exception
     *
     * @return \Cabbage\Core\Engine
     */
    protected function getEngineUnderTest(): Engine
    {
        return self::getContainer()->get('cabbage.engine');
    }

    /**
     * @throws \Exception
     *
     * @param \Cabbage\SPI\Node $node
     */
    protected function refresh(Node $node): void
    {
        self::getContainer()->get('cabbage.indexer.gateway')->refresh($node);
    }
}
