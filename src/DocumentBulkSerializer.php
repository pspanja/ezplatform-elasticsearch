<?php

declare(strict_types=1);

namespace Cabbage;

/**
 * Serializes an array of Document objects into a JSON string for bulk indexing.
 *
 * @see \Cabbage\Document
 */
final class DocumentBulkSerializer
{
    /**
     * @var \Cabbage\DocumentRouter
     */
    private $documentRouter;

    /**
     * @var \Cabbage\DocumentSerializer
     */
    private $documentSerializer;

    /**
     * @param \Cabbage\DocumentRouter $documentRouter
     * @param \Cabbage\DocumentSerializer $documentSerializer
     */
    public function __construct(
        DocumentRouter $documentRouter,
        DocumentSerializer $documentSerializer
    ) {
        $this->documentRouter = $documentRouter;
        $this->documentSerializer = $documentSerializer;
    }

    /**
     * @param \Cabbage\Document[] $documents
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
     * @param \Cabbage\Endpoint $endpoint
     * @param \Cabbage\Document $document
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
