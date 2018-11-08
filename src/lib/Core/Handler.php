<?php

declare(strict_types=1);

namespace Cabbage\Core;

use Cabbage\Core\Document\Serializer;
use Cabbage\Core\Document\Mapper;
use Cabbage\Core\Query\TargetResolver;
use Cabbage\Core\Query\Translator;
use Cabbage\SPI\Endpoint;
use eZ\Publish\API\Repository\Values\Content\LocationQuery;
use eZ\Publish\API\Repository\Values\Content\Query;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion;
use eZ\Publish\API\Repository\Values\Content\Search\SearchResult;
use eZ\Publish\SPI\Persistence\Content;
use eZ\Publish\SPI\Persistence\Content\ContentInfo;
use eZ\Publish\SPI\Persistence\Content\Location;
use eZ\Publish\SPI\Search\Capable;
use eZ\Publish\SPI\Search\Handler as HandlerInterface;
use RuntimeException;

final class Handler implements HandlerInterface, Capable
{
    /**
     * @var \Cabbage\Core\Gateway
     */
    private $gateway;

    /**
     * @var \Cabbage\Core\Document\Mapper
     */
    private $documentMapper;

    /**
     * @var \Cabbage\Core\Document\Serializer
     */
    private $documentBulkSerializer;

    /**
     * @var \Cabbage\Core\Query\Translator
     */
    private $queryTranslator;

    /**
     * @var \Cabbage\Core\Query\TargetResolver
     */
    private $targetResolver;

    /**
     * @var \Cabbage\Core\ResultExtractor
     */
    private $resultExtractor;

    /**
     * @param \Cabbage\Core\Gateway $gateway
     * @param \Cabbage\Core\Document\Mapper $documentMapper
     * @param \Cabbage\Core\Document\Serializer $documentBulkSerializer
     * @param \Cabbage\Core\Query\Translator $queryTranslator
     * @param \Cabbage\Core\Query\TargetResolver $targetResolver
     * @param \Cabbage\Core\ResultExtractor $resultExtractor
     */
    public function __construct(
        Gateway $gateway,
        Mapper $documentMapper,
        Serializer $documentBulkSerializer,
        Translator $queryTranslator,
        TargetResolver $targetResolver,
        ResultExtractor $resultExtractor
    ) {
        $this->gateway = $gateway;
        $this->documentMapper = $documentMapper;
        $this->documentBulkSerializer = $documentBulkSerializer;
        $this->queryTranslator = $queryTranslator;
        $this->targetResolver = $targetResolver;
        $this->resultExtractor = $resultExtractor;
    }

    /**
     * {@inheritdoc}
     */
    public function supports($capabilityFlag): bool
    {
        throw new RuntimeException('Not implemented');
    }

    /**
     * {@inheritdoc}
     */
    public function findContent(Query $query, array $languageFilter = []): SearchResult
    {
        return
            $this->resultExtractor->extract(
                $this->gateway->find(
                    $this->targetResolver->resolve($query),
                    $this->queryTranslator->translateContentQuery($query)
                )
            );
    }

    /**
     * {@inheritdoc}
     */
    public function findSingle(Criterion $filter, array $languageFilter = []): ContentInfo
    {
        throw new RuntimeException('Not implemented');
    }

    /**
     * {@inheritdoc}
     */
    public function findLocations(LocationQuery $query, array $languageFilter = []): SearchResult
    {
        return
            $this->resultExtractor->extract(
                $this->gateway->find(
                    $this->targetResolver->resolve($query),
                    $this->queryTranslator->translateLocationQuery($query)
                )
            );
    }

    /**
     * {@inheritdoc}
     */
    public function suggest($prefix, $fieldPaths = [], $limit = 10, ?Criterion $filter = null): void
    {
        throw new RuntimeException('Not implemented');
    }

    public function indexContent(Content $content): void
    {
        $documents = $this->documentMapper->map($content);

        $payload = $this->documentBulkSerializer->serialize($documents);

        $this->gateway->bulkIndex(Endpoint::fromDsn('http://localhost:9200/index'), $payload);
    }

    /**
     * {@inheritdoc}
     */
    public function deleteContent($contentId, $versionId = null): void
    {
        throw new RuntimeException('Not implemented');
    }

    public function indexLocation(Location $location): void
    {
        throw new RuntimeException('Not implemented');
    }

    /**
     * {@inheritdoc}
     */
    public function deleteLocation($locationId, $contentId): void
    {
        throw new RuntimeException('Not implemented');
    }

    public function purgeIndex(): void
    {
        throw new RuntimeException('Not implemented');
    }
}
