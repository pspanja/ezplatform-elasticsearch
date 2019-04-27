<?php

declare(strict_types=1);

namespace Cabbage\Tests\Integration\Core\Searcher;

use Cabbage\Core\Searcher\Target;
use Cabbage\SPI\Index;
use Cabbage\SPI\Node;
use Cabbage\Tests\Integration\Core\BaseTest;
use function file_get_contents;

class GatewayTest extends BaseTest
{
    /**
     * @var \Cabbage\SPI\Index
     */
    private static $index;

    /**
     * @var \Cabbage\Core\Indexer\Gateway
     */
    private static $indexerGateway;

    /**
     * @var \Cabbage\Core\Searcher\Gateway
     */
    private static $gateway;

    /**
     * @throws \Exception
     */
    public static function setUpBeforeClass(): void
    {
        $node = Node::fromDsn('http://localhost:9200');
        self::$index = new Index($node, 'searcher_gateway_test');
        self::$indexerGateway = self::getContainer()->get('cabbage.indexer.gateway');
        self::$gateway = self::getContainer()->get('cabbage.searcher.gateway');
        $configurator = self::getContainer()->get('cabbage.configurator');

        if ($configurator->hasIndex(self::$index)) {
            $configurator->deleteIndex(self::$index);
        }

        $mapping = file_get_contents(__DIR__ . '/../../../../config/elasticsearch/mapping.json');

        $configurator->createIndex(self::$index);
        $configurator->setMapping(self::$index, $mapping);
    }

    /**
     * @testdox All documents can be found
     * @Depends Cabbage\Tests\Integration\Core\Indexer\GatewayTest::testBulkIndex
     *
     * @throws \Exception
     */
    public function testFindAll(): void
    {
        $index = self::$index;
        $payload = <<<EOD
{"index":{"_index":"{$index->name}","_id":"a_1"}}
{"type_identifier":"type_a","field_keyword":"value"}
{"index":{"_index":"{$index->name}","_id":"b_1"}}
{"type_identifier":"type_b","field_keyword":"value"}

EOD;

        self::$indexerGateway->index($index, $payload);
        self::$indexerGateway->refresh($index);

        $query = [
            'query' => [
                'match_all' => (object)null,
            ],
        ];

        $target = new Target($index->node, [$index]);

        $data = self::$gateway->find($target, $query);
        $data = json_decode($data, false);

        $this->assertEquals(2, $data->hits->total->value);
    }

    /**
     * @testdox Documents can be found by existing type
     * @depends testFindAll
     *
     * @throws \Exception
     */
    public function testFindByType(): void
    {
        $query = [
            'query' => [
                'term' => [
                    'type_identifier' => 'type_b',
                ],
            ],
        ];

        $target = new Target(self::$index->node, [self::$index]);

        $data = self::$gateway->find($target, $query);
        $data = json_decode($data, false);

        $this->assertEquals(1, $data->hits->total->value);
    }

    /**
     * @testdox Documents can't be found by nonexistent type
     * @depends testFindAll
     */
    public function testFindNoneByNonexistentType(): void
    {
        $query = [
            'query' => [
                'term' => [
                    'type_identifier' => 'type_c',
                ],
            ],
        ];

        $target = new Target(self::$index->node, [self::$index]);

        $data = self::$gateway->find($target, $query);
        $data = json_decode($data, false);

        $this->assertEquals(0, $data->hits->total->value);
    }

    /**
     * @testdox Documents can't be found if the index is empty
     * @depends testFindAll
     */
    public function testFindNoneAfterPurge(): void
    {
        self::$indexerGateway->purge(self::$index);
        self::$indexerGateway->refresh(self::$index);

        $query = [
            'query' => [
                'match_all' => (object)null,
            ],
        ];

        $target = new Target(self::$index->node, [self::$index]);

        $data = self::$gateway->find($target, $query);
        $data = json_decode($data, false);

        $this->assertEquals(0, $data->hits->total->value);
    }
}
