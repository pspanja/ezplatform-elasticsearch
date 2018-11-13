<?php

declare(strict_types=1);

namespace Cabbage\Tests\Integration\API;

use eZ\Publish\API\Repository\Repository;
use eZ\Publish\API\Repository\Tests\BaseTest;

final class TestTest extends BaseTest
{
    public function testTest(): void
    {
        $repository = $this->getRepository();

        $this->assertInstanceOf(Repository::class, $repository);
    }
}
