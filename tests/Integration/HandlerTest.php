<?php

declare(strict_types=1);

namespace Cabbage\Tests\Integration;

use Cabbage\DocumentMapper;
use Cabbage\DocumentRouter;
use Cabbage\DocumentSerializer;
use Cabbage\Endpoint;
use Cabbage\Gateway;
use Cabbage\Handler;
use Cabbage\Http\Client;
use Cabbage\QueryRouter;
use Cabbage\QueryTranslator;
use Cabbage\ResultExtractor;
use eZ\Publish\API\Repository\Values\Content\Query;
use eZ\Publish\SPI\Persistence\Content;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerInterface;

class HandlerTest extends BaseTest
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
        $endpoint = Endpoint::fromDsn('http://localhost:9200/index');
        $handler = $this->getHandlerUnderTest();
        $gateway = $this->getGateway();

        /** @var \eZ\Publish\SPI\Persistence\Content $content */
        $content = $this->getMockBuilder(Content::class)->getMock();

        $handler->indexContent($content);
        $gateway->flush($endpoint);

        /** @var \eZ\Publish\API\Repository\Values\Content\Query $query */
        $query = $this->getMockBuilder(Query::class)->getMock();

        $searchResult = $handler->findContent($query);

        $this->assertGreaterThanOrEqual(1, $searchResult->totalCount);
    }

    public function getHandlerUnderTest(): Handler
    {
        return new Handler(
            $this->getGateway(),
            new DocumentMapper(),
            new DocumentRouter(),
            new QueryTranslator(),
            new QueryRouter(),
            new ResultExtractor()
        );
    }

    public function testContainer(): void
    {
        $container = $this->getContainer();

        $parameter = $container->getParameter('test');

        $this->assertEquals('bla bla', $parameter);
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
