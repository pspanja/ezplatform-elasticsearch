<?php

declare(strict_types=1);

namespace Cabbage\Core\Indexer\Document;

use Cabbage\Core\Indexer\Document\Field\TypedNameGenerator;
use Cabbage\Core\Indexer\Document\Field\ValueMapper;
use Cabbage\SPI\Document;

/**
 * Serializes an array of Document objects into a JSON string for bulk indexing.
 *
 * @see \Cabbage\SPI\Document
 */
final class Serializer
{
    /**
     * @var \Cabbage\Core\Indexer\Document\Field\TypedNameGenerator
     */
    private $fieldTypedNameGenerator;

    /**
     * @var \Cabbage\Core\Indexer\Document\Field\ValueMapper
     */
    private $fieldValueMapper;

    /**
     * @param \Cabbage\Core\Indexer\Document\Field\TypedNameGenerator $fieldTypedNameGenerator
     * @param \Cabbage\Core\Indexer\Document\Field\ValueMapper $fieldValueMapper
     */
    public function __construct(
        TypedNameGenerator $fieldTypedNameGenerator,
        ValueMapper $fieldValueMapper
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

        return json_encode($data, JSON_THROW_ON_ERROR);
    }
}
