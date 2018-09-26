<?php

declare(strict_types=1);

namespace Cabbage\Tests\Integration;

use Cabbage\DocumentMapper;
use Cabbage\DocumentSerializer;
use Cabbage\Gateway;
use Cabbage\Handler;
use Cabbage\Http\Client;
use eZ\Publish\API\Repository\Values\Content\Query;
use eZ\Publish\SPI\Persistence\Content;
use PHPUnit\Framework\TestCase;

class HandlerTest extends TestCase
{
    public function testIndexContent(): void
    {
        $handler = $this->getHandlerUnderTest();
        /** @var \eZ\Publish\SPI\Persistence\Content $content */
        $content = $this->getMockBuilder(Content::class)->getMock();

        $handler->indexContent($content);

        $this->assertTrue(true);
    }

    /**
     * @depends testIndexContent
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     */
    public function testFindContent(): void
    {
        $handler = $this->getHandlerUnderTest();
        $gateway = $this->getGateway();

        /** @var \eZ\Publish\SPI\Persistence\Content $content */
        $content = $this->getMockBuilder(Content::class)->getMock();

        $handler->indexContent($content);
        $gateway->flush('http://localhost:9200');

        /** @var \eZ\Publish\API\Repository\Values\Content\Query $query */
        $query = $this->getMockBuilder(Query::class)->getMock();

        $searchResult = $handler->findContent($query);

        $this->assertGreaterThanOrEqual(1, $searchResult->totalCount);
    }

    public function getHandlerUnderTest(): Handler
    {
        return new Handler(
            $this->getGateway(),
            new DocumentMapper()
        );
    }

    /**
     * @var \Cabbage\Gateway
     */
    protected $gateway;

    protected function getGateway(): Gateway
    {
        if ($this->gateway === null) {
            $this->gateway = new Gateway(
                new Client(),
                new DocumentSerializer()
            );
        }

        return $this->gateway;
    }
}
