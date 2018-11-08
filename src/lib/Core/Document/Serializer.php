<?php

declare(strict_types=1);

namespace Cabbage\Core\Document;

use Cabbage\Core\Document\Serializer\Router;
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
     * @var \Cabbage\Core\Document\Serializer\Router
     */
    private $documentRouter;

    /**
     * @var \Cabbage\Core\Document\Serializer\FieldSerializer
     */
    private $fieldSerializer;

    /**
     * @param \Cabbage\Core\Document\Serializer\Router $documentRouter
     * @param \Cabbage\Core\Document\Serializer\FieldSerializer $fieldSerializer
     */
    public function __construct(
        Router $documentRouter,
        FieldSerializer $fieldSerializer
    ) {
        $this->documentRouter = $documentRouter;
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
        $endpoint = $this->documentRouter->match($document);

        $metaData = $this->getTargetMetadata($endpoint, $document);
        $payload = $this->fieldSerializer->serialize($document);

        return "{$metaData}\n{$payload}\n";
    }

    /**
     * Generate action and metadata for the indexed Document.
     *
     * @param \Cabbage\SPI\Endpoint $endpoint
     * @param \Cabbage\SPI\Document $document
     *
     * @return string
     */
    private function getTargetMetadata(Endpoint $endpoint, Document $document): string
    {
        return json_encode([
            'index' => [
                '_index' => $endpoint->index,
                '_type' => '_doc',
                '_id' => $document->id,
            ],
        ]);
    }
}
