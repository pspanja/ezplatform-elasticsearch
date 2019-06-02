<?php

declare(strict_types=1);

namespace Cabbage\Core\Indexer;

use Cabbage\Core\Indexer\DocumentSerializer\TypedFieldNameGenerator;
use Cabbage\Core\Indexer\DocumentSerializer\FieldValueMapper;
use Cabbage\SPI\Document;

/**
 * Serializes a document into a JSON string for bulk indexing.
 *
 * @see \Cabbage\SPI\Document
 */
final class DocumentSerializer
{
    /**
     * @var \Cabbage\Core\Indexer\DocumentSerializer\TypedFieldNameGenerator
     */
    private $fieldTypedNameGenerator;

    /**
     * @var \Cabbage\Core\Indexer\DocumentSerializer\FieldValueMapper
     */
    private $fieldValueMapper;

    /**
     * @param \Cabbage\Core\Indexer\DocumentSerializer\TypedFieldNameGenerator $fieldTypedNameGenerator
     * @param \Cabbage\Core\Indexer\DocumentSerializer\FieldValueMapper $fieldValueMapper
     */
    public function __construct(
        TypedFieldNameGenerator $fieldTypedNameGenerator,
        FieldValueMapper $fieldValueMapper
    ) {
        $this->fieldTypedNameGenerator = $fieldTypedNameGenerator;
        $this->fieldValueMapper = $fieldValueMapper;
    }

    /**
     * @param \Cabbage\SPI\Document $document
     *
     * @return string
     */
    public function serialize(Document $document): string
    {
        $data = [];

        foreach ($document->fields as $field) {
            $fieldName = $this->fieldTypedNameGenerator->generate($field);
            $fieldValue = $this->fieldValueMapper->map($field);

            $data[$fieldName] = $fieldValue;
        }

        $actionAndMetaData = $this->getActionAndMetaData($document);
        $payload = json_encode($data, JSON_THROW_ON_ERROR);

        return $actionAndMetaData . "\n" . $payload . "\n";
    }

    /**
     * Generate action and metadata for the indexed Document.
     *
     * @param \Cabbage\SPI\Document $document
     *
     * @return string
     */
    private function getActionAndMetaData(Document $document): string
    {
        $data = [
            'index' => [
                '_index' => $document->index,
                '_id' => $document->id,
            ],
        ];

        return json_encode($data, JSON_THROW_ON_ERROR);
    }
}
