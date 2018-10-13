<?php

declare(strict_types=1);

namespace Cabbage\Tests\Integration;

use Cabbage\Endpoint;
use Cabbage\Handler;
use eZ\Publish\API\Repository\Values\Content\LocationQuery;
use eZ\Publish\API\Repository\Values\Content\Query;
use eZ\Publish\SPI\Persistence\Content;
use eZ\Publish\SPI\Persistence\Content\ContentInfo;
use eZ\Publish\SPI\Persistence\Content\VersionInfo;

class HandlerTest extends BaseTest
{
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
                    'id' => 'CONTENT_ID'
                ]),
            ]),
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

        /** @var \eZ\Publish\API\Repository\Values\Content\Query $query */
        $query = $this->getMockBuilder(Query::class)->getMock();

        $searchResult = $handler->findContent($query);

        $this->assertGreaterThanOrEqual(1, $searchResult->totalCount);
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

        /** @var \eZ\Publish\API\Repository\Values\Content\LocationQuery $query */
        $query = $this->getMockBuilder(LocationQuery::class)->getMock();

        $searchResult = $handler->findLocations($query);

        $this->assertGreaterThanOrEqual(1, $searchResult->totalCount);
    }

    /**
     * @throws \Exception
     *
     * @return \Cabbage\Handler
     */
    protected function getHandlerUnderTest(): Handler
    {
        return self::getContainer()->get('cabbage.handler');
    }

    /**
     * @throws \Exception
     *
     * @param \Cabbage\Endpoint $endpoint
     */
    protected function flush(Endpoint $endpoint): void
    {
        self::getContainer()->get('cabbage.gateway')->flush($endpoint);
    }
}
