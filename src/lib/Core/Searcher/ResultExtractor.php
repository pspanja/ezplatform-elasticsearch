<?php

declare(strict_types=1);

namespace Cabbage\Core\Searcher;

use eZ\Publish\API\Repository\Values\Content\Search\SearchHit;
use eZ\Publish\API\Repository\Values\Content\Search\SearchResult;
use eZ\Publish\API\Repository\Values\ValueObject;
use eZ\Publish\SPI\Persistence\Content\ContentInfo;
use eZ\Publish\SPI\Persistence\Content\Location;
use function json_decode;
use RuntimeException;

/**
 * Extracts search result from the raw response string.
 *
 * @see \eZ\Publish\API\Repository\Values\Content\Search\SearchResult
 */
final class ResultExtractor
{
    /**
     * @param string $data
     *
     * @return \eZ\Publish\API\Repository\Values\Content\Search\SearchResult
     */
    public function extract(string $data): SearchResult
    {
        $body = json_decode($data, false, 512, JSON_THROW_ON_ERROR);
        $searchHits = [];

        foreach ($body->hits->hits as $hit) {
            $searchHits[] = new SearchHit([
                'index' => $hit->_index,
                'valueObject' => $this->extractSearchHit($hit),
            ]);
        }

        return new SearchResult([
            'searchHits' => $searchHits,
            'totalCount' => $body->hits->total->value,
        ]);
    }

    /**
     * @see https://www.elastic.co/guide/en/elasticsearch/reference/master/search-request-body.html
     *
     * @param object $hit
     *
     * @return \eZ\Publish\API\Repository\Values\ValueObject
     */
    private function extractSearchHit(object $hit): ValueObject
    {
        if ($hit->_source->type_identifier === 'content') {
            return $this->extractContentInfo($hit);
        }

        if ($hit->_source->type_identifier === 'location') {
            return $this->extractLocation($hit);
        }

        throw new RuntimeException(
            "Document of type '{$hit->_source->type_identifier}' is not handled"
        );
    }

    private function extractContentInfo(object $hit): ContentInfo
    {
        return new ContentInfo([
            'id' => $hit->_source->content_id_identifier,
        ]);
    }

    private function extractLocation(object $hit): Location
    {
        return new Location([
            'id' => $hit->_source->location_id_identifier,
        ]);
    }
}
