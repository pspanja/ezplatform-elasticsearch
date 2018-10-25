<?php

declare(strict_types=1);

namespace Cabbage\Core\Document;

use Cabbage\SPI\Document;
use Cabbage\SPI\Field;
use RuntimeException;

/**
 * Serializes a Document object into a JSON string for indexing.
 *
 * @see \Cabbage\SPI\Document
 */
final class Serializer
{
    /**
     * @var \Cabbage\Core\Document\FieldNameGenerator
     */
    private $fieldNameGenerator;

    /**
     * @var \Cabbage\Core\Document\FieldValueMapper
     */
    private $fieldValueMapper;

    /**
     * @param \Cabbage\Core\Document\FieldNameGenerator $fieldNameGenerator
     * @param \Cabbage\Core\Document\FieldValueMapper $fieldValueMapper
     */
    public function __construct(
        FieldNameGenerator $fieldNameGenerator,
        FieldValueMapper $fieldValueMapper
    ) {
        $this->fieldValueMapper = $fieldValueMapper;
        $this->fieldNameGenerator = $fieldNameGenerator;
    }

    /**
     * @param \Cabbage\SPI\Document $document
     *
     * @return string
     */
    public function serialize(Document $document): string
    {
        $data = [
            'type' => $document->type,
        ];

        foreach ($document->fields as $field) {
            $fieldName = $this->fieldNameGenerator->generate($field);
            $fieldValue = $this->fieldValueMapper->map($field);

            $data[$fieldName] = $fieldValue;
        }

        $data = json_encode($data);

        if ($data === false) {
            throw new RuntimeException('Could not JSON encode given document');
        }

        return $data;
    }
}
