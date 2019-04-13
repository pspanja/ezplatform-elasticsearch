<?php

declare(strict_types=1);

namespace Cabbage\Core\Indexer\Document;

use Cabbage\Core\Indexer\Document\Field\TypedNameGenerator;
use Cabbage\Core\Indexer\Document\Field\ValueMapper;
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
     * @var \Cabbage\Core\Indexer\Document\DestinationResolver
     */
    private $destinationResolver;

    /**
     * @var \Cabbage\Core\Indexer\Document\Field\TypedNameGenerator
     */
    private $fieldTypedNameGenerator;

    /**
     * @var \Cabbage\Core\Indexer\Document\Field\ValueMapper
     */
    private $fieldValueMapper;

    /**
     * @param \Cabbage\Core\Indexer\Document\DestinationResolver $destinationResolver
     * @param \Cabbage\Core\Indexer\Document\Field\TypedNameGenerator $fieldTypedNameGenerator
     * @param \Cabbage\Core\Indexer\Document\Field\ValueMapper $fieldValueMapper
     */
    public function __construct(
        DestinationResolver $destinationResolver,
        TypedNameGenerator $fieldTypedNameGenerator,
        ValueMapper $fieldValueMapper
    ) {
        $this->destinationResolver = $destinationResolver;
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
        $result = '';

        foreach ($documents as $document) {
            $result .= $this->serializeDocument($document);
        }

        return $result;
    }

    private function serializeDocument(Document $document): string
    {
        $index = $this->destinationResolver->resolve($document);

        $actionAndMetaData = $this->getActionAndMetaData($index, $document);
        $payload = $this->getPayload($document);

        return "{$actionAndMetaData}\n{$payload}\n";
    }

    /**
     * Generate action and metadata for the indexed Document.
     *
     * @param \Cabbage\SPI\Index $index
     * @param \Cabbage\SPI\Document $document
     *
     * @return string
     */
    private function getActionAndMetaData(Index $index, Document $document): string
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
    private function getPayload(Document $document): string
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
