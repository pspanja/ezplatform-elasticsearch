<?php

declare(strict_types=1);

namespace Cabbage\Core\Document;

use Cabbage\Core\Document\Serializer\IndexResolver;
use Cabbage\Core\Document\Serializer\FieldSerializer;
use Cabbage\SPI\Document;
use Cabbage\SPI\Endpoint;

/**
 * Serializes an array of Document objects into a JSON string for bulk indexing.
 *
 * @see \Cabbage\SPI\Document
 */
final class Serializer
{
    /**
     * @var \Cabbage\Core\Document\Serializer\IndexResolver
     */
    private $indexResolver;

    /**
     * @var \Cabbage\Core\Document\Serializer\FieldSerializer
     */
    private $fieldSerializer;

    /**
     * @param \Cabbage\Core\Document\Serializer\IndexResolver $indexResolver
     * @param \Cabbage\Core\Document\Serializer\FieldSerializer $fieldSerializer
     */
    public function __construct(
        IndexResolver $indexResolver,
        FieldSerializer $fieldSerializer
    ) {
        $this->indexResolver = $indexResolver;
        $this->fieldSerializer = $fieldSerializer;
    }

    /**
     * @param \Cabbage\SPI\Document[] $documents
     *
     * @return string
     */
    public function serialize(array $documents): string
    {
        $payload = '';

        foreach ($documents as $document) {
            $payload .= $this->getDocumentPayload($document);
        }

        return $payload;
    }

    private function getDocumentPayload(Document $document): string
    {
        $index = $this->indexResolver->resolve($document);

        $targetMetaData = $this->getTargetMetadata($index, $document);
        $fieldPayload = $this->fieldSerializer->serialize($document);

        return "{$targetMetaData}\n{$fieldPayload}\n";
    }

    /**
     * Generate action and metadata for the indexed Document.
     *
     * @param \Cabbage\SPI\Endpoint $index
     * @param \Cabbage\SPI\Document $document
     *
     * @return string
     */
    private function getTargetMetadata(Endpoint $index, Document $document): string
    {
        return json_encode(
            [
                'index' => [
                    '_index' => $index->index,
                    '_type' => '_doc',
                    '_id' => $document->id,
                ],
            ],
            JSON_THROW_ON_ERROR
        );
    }
}
