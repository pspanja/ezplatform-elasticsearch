<?php

declare(strict_types=1);

namespace Cabbage\Core\Document;

use Cabbage\Core\Document\Serializer\FieldNameGenerator;
use Cabbage\Core\Document\Serializer\ValueMapper;
use Cabbage\Core\Document\Serializer\IndexResolver;
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
     * @var \Cabbage\Core\Document\Serializer\FieldNameGenerator
     */
    private $fieldNameGenerator;

    /**
     * @var \Cabbage\Core\Document\Serializer\ValueMapper
     */
    private $fieldValueMapper;

    /**
     * @param \Cabbage\Core\Document\Serializer\IndexResolver $indexResolver
     * @param \Cabbage\Core\Document\Serializer\FieldNameGenerator $fieldNameGenerator
     * @param \Cabbage\Core\Document\Serializer\ValueMapper $fieldValueMapper
     */
    public function __construct(
        IndexResolver $indexResolver,
        FieldNameGenerator $fieldNameGenerator,
        ValueMapper $fieldValueMapper
    ) {
        $this->indexResolver = $indexResolver;
        $this->fieldValueMapper = $fieldValueMapper;
        $this->fieldNameGenerator = $fieldNameGenerator;
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
        $fieldPayload = $this->serializeDocument($document);

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
        $data = [
            'index' => [
                '_index' => $index->index,
                '_type' => '_doc',
                '_id' => $document->id,
            ],
        ];

        return json_encode($data, JSON_THROW_ON_ERROR);
    }

    /**
     * @param \Cabbage\SPI\Document $document
     *
     * @return string
     */
    private function serializeDocument(Document $document): string
    {
        $data = [
            'type' => $document->type,
        ];

        foreach ($document->fields as $field) {
            $fieldName = $this->fieldNameGenerator->generate($field);
            $fieldValue = $this->fieldValueMapper->map($field);

            $data[$fieldName] = $fieldValue;
        }

        return json_encode($data, JSON_THROW_ON_ERROR);
    }
}
