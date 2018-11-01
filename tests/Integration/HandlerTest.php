<?php

declare(strict_types=1);

namespace Cabbage\Tests\Integration;

use Cabbage\API\Query\Criterion\DocumentType;
use Cabbage\Core\Handler;
use Cabbage\SPI\Document;
use Cabbage\SPI\Endpoint;
use eZ\Publish\API\Repository\Values\Content\LocationQuery;
use eZ\Publish\API\Repository\Values\Content\Query;
use eZ\Publish\SPI\Persistence\Content;
use eZ\Publish\SPI\Persistence\Content\ContentInfo;
use eZ\Publish\SPI\Persistence\Content\VersionInfo;

class HandlerTest extends BaseTest
{
    /**
     * @var \Cabbage\SPI\Endpoint
     */
    private static $endpoint;

    /**
     * @var \Cabbage\Core\Gateway
     */
    private static $gateway;

    /**
     * @throws \Exception
     */
    public static function setUpBeforeClass(): void
    {
        self::$endpoint = Endpoint::fromDsn('http://localhost:9200/index');
        self::$gateway = self::getContainer()->get('cabbage.gateway');
        $configurator = self::getContainer()->get('cabbage.configurator');

        if ($configurator->hasIndex(self::$endpoint)) {
            $configurator->deleteIndex(self::$endpoint);
        }

        $mapping = \file_get_contents(__DIR__ . '/../../config/elasticsearch/mapping.json');

        $configurator->createIndex(self::$endpoint);
        $configurator->setMapping(self::$endpoint, $mapping);
    }

    /**
     * @testdox Content can be indexed
     *
     * @throws \Exception
     */
    public function testIndexContent(): void
    {
        $endpoint = Endpoint::fromDsn('http://localhost:9200/index');
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
        $this->flush($endpoint);

        $this->addToAssertionCount(1);
    }

    /**
     * @testdox Content can be found
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

        $this->assertGreaterThanOrEqual(1, $searchResult->totalCount);
        $this->assertEquals(
            'content_CONTENT_ID',
            $searchResult->searchHits[0]->valueObject->_id
        );
    }

    /**
     * @testdox Locations can be found
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

        $this->assertGreaterThanOrEqual(1, $searchResult->totalCount);
        $this->assertEquals(
            'location_LOCATION_ID',
            $searchResult->searchHits[0]->valueObject->_id
        );
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
     * @param \Cabbage\SPI\Endpoint $endpoint
     */
    protected function flush(Endpoint $endpoint): void
    {
        self::getContainer()->get('cabbage.gateway')->flush($endpoint);
    }
}
