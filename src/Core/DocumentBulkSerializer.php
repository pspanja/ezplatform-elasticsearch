<?php

declare(strict_types=1);

namespace Cabbage\Core;

use Cabbage\SPI\Document;
use Cabbage\SPI\Endpoint;

/**
 * Serializes an array of Document objects into a JSON string for bulk indexing.
 *
 * @see \Cabbage\SPI\Document
 */
final class DocumentBulkSerializer
{
    /**
     * @var \Cabbage\Core\DocumentRouter
     */
    private $documentRouter;

    /**
     * @var \Cabbage\Core\DocumentSerializer
     */
    private $documentSerializer;

    /**
     * @param \Cabbage\Core\DocumentRouter $documentRouter
     * @param \Cabbage\Core\DocumentSerializer $documentSerializer
     */
    public function __construct(
        DocumentRouter $documentRouter,
        DocumentSerializer $documentSerializer
    ) {
        $this->documentRouter = $documentRouter;
        $this->documentSerializer = $documentSerializer;
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

        $metaData = $this->getMetaData($endpoint, $document);
        $payload = $this->documentSerializer->serialize($document);

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
    private function getMetaData(Endpoint $endpoint, Document $document): string
    {
        return json_encode([
            'index' => [
                '_index' => $endpoint->index,
                '_type' => 'temporary',
                '_id' => $document->id,
            ],
        ]);
    }
}
