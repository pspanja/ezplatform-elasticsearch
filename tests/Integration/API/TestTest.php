<?php

declare(strict_types=1);

namespace Cabbage\Tests\Integration\API;

use Cabbage\SPI\Endpoint;
use eZ\Publish\API\Repository\Repository;
use eZ\Publish\API\Repository\Tests\BaseTest;
use eZ\Publish\API\Repository\Values\Content\Query;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\ContentId;

final class TestTest extends BaseTest
{
    public function testTest(): void
    {
        $repository = $this->getRepository();

        $this->assertInstanceOf(Repository::class, $repository);
    }

    /**
     * @throws \eZ\Publish\API\Repository\Exceptions\BadStateException
     * @throws \eZ\Publish\API\Repository\Exceptions\ContentFieldValidationException
     * @throws \eZ\Publish\API\Repository\Exceptions\ContentValidationException
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     * @throws \Exception
     */
    public function testFindContentById(): void
    {
        $repository = $this->getRepository();
        $contentService = $repository->getContentService();
        $contentTypeService = $repository->getContentTypeService();
        $searchService = $repository->getSearchService();

        $mainLanguageCode = 'eng-GB';

        $struct = $contentService->newContentCreateStruct(
            $contentTypeService->loadContentTypeByIdentifier('folder'),
            $mainLanguageCode
        );

        $struct->setField('name', 'folder', $mainLanguageCode);

        $contentDraft = $contentService->createContent($struct);
        $content = $contentService->publishVersion($contentDraft->versionInfo);

        $this->flush(Endpoint::fromDsn('http://localhost:9200/index'));

        $query = new Query([
            'filter' => new ContentId($content->id),
        ]);

        $searchResult = $searchService->findContentInfo($query);

        $this->assertEquals(1, $searchResult->totalCount);
        $this->assertEquals($content->id, $searchResult->searchHits[0]->valueObject->id);
    }

    /**
     * @throws \Exception
     *
     * @param \Cabbage\SPI\Endpoint $endpoint
     */
    protected function flush(Endpoint $endpoint): void
    {
        $this->getSetupFactory()->getServiceContainer()->get('cabbage.gateway')->flush($endpoint);
    }
}
