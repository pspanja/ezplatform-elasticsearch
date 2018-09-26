<?php

declare(strict_types=1);

namespace Cabbage\Tests\Integration;

use Cabbage\DocumentMapper;
use Cabbage\DocumentSerializer;
use Cabbage\Gateway;
use Cabbage\Handler;
use Cabbage\Http\Client;
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

    public function getHandlerUnderTest(): Handler
    {
        return new Handler(
            new Gateway(
                new Client(),
                new DocumentSerializer()
            ),
            new DocumentMapper()
        );
    }
}
