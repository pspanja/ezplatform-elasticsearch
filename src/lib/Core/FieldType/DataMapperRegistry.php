<?php

declare(strict_types=1);

namespace Cabbage\Core\FieldType;

use Cabbage\SPI\FieldType\DataMapper;
use OutOfBoundsException;

/**
 * FieldType DataMapper registry.
 *
 * @see \Cabbage\SPI\FieldType\DataMapper
 */
final class DataMapperRegistry
{
    /**
     * A map of data mappers by identifier.
     *
     * @param \Cabbage\SPI\FieldType\DataMapper[]
     */
    private $dataMappersByIdentifier = [];

    /**
     * @param \Cabbage\SPI\FieldType\DataMapper[]
     */
    public function __construct(array $dataMappers = [])
    {
        foreach ($dataMappers as $identifier => $dataMapper) {
            $this->addDataMapper($identifier, $dataMapper);
        }
    }

    /**
     * Add data mapper to the internal map by identifier.
     *
     * @param string $identifier
     * @param \Cabbage\SPI\FieldType\DataMapper $dataMapper
     */
    private function addDataMapper(string $identifier, DataMapper $dataMapper): void
    {
        $this->dataMappersByIdentifier[$identifier] = $dataMapper;
    }

    /**
     * Get DataMapper by the FieldType identifier.
     *
     * @param string $identifier
     *
     * @return \Cabbage\SPI\FieldType\DataMapper
     */
    public function get(string $identifier): DataMapper
    {
        if (array_key_exists($identifier, $this->dataMappersByIdentifier)) {
            return $this->dataMappersByIdentifier[$identifier];
        }

        throw new OutOfBoundsException(
            "Data mapper for '{$identifier}' not found"
        );
    }
}
