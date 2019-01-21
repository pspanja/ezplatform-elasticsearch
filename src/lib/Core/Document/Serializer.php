<?php

declare(strict_types=1);

namespace Cabbage\Core\Document;

use Cabbage\Core\Document\Field\TypedNameGenerator;
use Cabbage\Core\Document\Field\ValueMapper;
use Cabbage\SPI\Document;
use Cabbage\SPI\Index;

/**
 * Serializes an array of Document objects into a JSON string for bulk indexing.
 *
 * @see \Cabbage\SPI\Document
 */
final class Serializer
{
    /**
     * @var \Cabbage\Core\Document\IndexResolver
     */
    private $documentIndexResolver;

    /**
     * @var \Cabbage\Core\Document\Field\TypedNameGenerator
     */
    private $fieldTypedNameGenerator;

    /**
     * @var \Cabbage\Core\Document\Field\ValueMapper
     */
    private $fieldValueMapper;

    /**
     * @param \Cabbage\Core\Document\IndexResolver $documentIndexResolver
     * @param \Cabbage\Core\Document\Field\TypedNameGenerator $fieldTypedNameGenerator
     * @param \Cabbage\Core\Document\Field\ValueMapper $fieldValueMapper
     */
    public function __construct(
        IndexResolver $documentIndexResolver,
        TypedNameGenerator $fieldTypedNameGenerator,
        ValueMapper $fieldValueMapper
    ) {
        $this->documentIndexResolver = $documentIndexResolver;
        $this->fieldTypedNameGenerator = $fieldTypedNameGenerator;
        $this->fieldValueMapper = $fieldValueMapper;
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
        $index = $this->documentIndexResolver->resolve($document);

        $targetMetaData = $this->getTargetMetadata($index, $document);
        $fieldPayload = $this->serializeDocument($document);

        return "{$targetMetaData}\n{$fieldPayload}\n";
    }

    /**
     * Generate action and metadata for the indexed Document.
     *
     * @param \Cabbage\SPI\Index $index
     * @param \Cabbage\SPI\Document $document
     *
     * @return string
     */
    private function getTargetMetadata(Index $index, Document $document): string
    {
        $data = [
            'index' => [
                '_index' => $index->name,
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
        $data = [];

        foreach ($document->fields as $field) {
            $fieldName = $this->fieldTypedNameGenerator->generate($field);
            $fieldValue = $this->fieldValueMapper->map($field);

            $data[$fieldName] = $fieldValue;
        }

        return json_encode($data, JSON_THROW_ON_ERROR);
    }
}
