<?php

declare(strict_types=1);

namespace Cabbage\Core\Document;

use Cabbage\Core\Document\Field\TypedNameGenerator;
use Cabbage\Core\Document\Field\ValueMapper;
use Cabbage\SPI\Document;
use RuntimeException;

/**
 * Serializes a Document object into a JSON string for indexing.
 *
 * @see \Cabbage\SPI\Document
 */
final class Serializer
{
    /**
     * @var \Cabbage\Core\Document\Field\TypedNameGenerator
     */
    private $fieldTypedNameGenerator;

    /**
     * @var \Cabbage\Core\Document\Field\ValueMapper
     */
    private $fieldValueMapper;

    /**
     * @param \Cabbage\Core\Document\Field\TypedNameGenerator $fieldTypedNameGenerator
     * @param \Cabbage\Core\Document\Field\ValueMapper $fieldValueMapper
     */
    public function __construct(
        TypedNameGenerator $fieldTypedNameGenerator,
        ValueMapper $fieldValueMapper
    ) {
        $this->fieldValueMapper = $fieldValueMapper;
        $this->fieldTypedNameGenerator = $fieldTypedNameGenerator;
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
            $fieldName = $this->fieldTypedNameGenerator->generate($field);
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
