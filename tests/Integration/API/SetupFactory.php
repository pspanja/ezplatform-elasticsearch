<?php

declare(strict_types=1);

namespace Cabbage\Tests\Integration\API;

use function assert;
use Cabbage\Core\Engine;
use Doctrine\DBAL\Connection;
use eZ\Publish\API\Repository\Repository;
use eZ\Publish\API\Repository\Tests\SetupFactory\Legacy as CoreSetupFactory;
use eZ\Publish\Core\Base\Container\Compiler;
use eZ\Publish\SPI\Persistence\Handler as PersistenceHandler;
use PDO;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\Config\FileLocator;

final class SetupFactory extends CoreSetupFactory
{
    /**
     * {@inheritdoc}
     *
     * @throws \Exception
     */
    public function getRepository($initializeFromScratch = true): Repository
    {
        $repository = parent::getRepository($initializeFromScratch);

        if ($initializeFromScratch) {
            $this->reindex();
        }

        return $repository;
    }

    /**
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $containerBuilder
     *
     * @throws \Exception
     */
    protected function externalBuildContainer(ContainerBuilder $containerBuilder): void
    {
        $settingsPath = __DIR__ . '/../../../config/symfony/';

        $solrLoader = new YamlFileLoader($containerBuilder, new FileLocator($settingsPath));
        $solrLoader->load('services.yml');

        $containerBuilder->addCompilerPass(new Compiler\Search\SearchEngineSignalSlotPass('cabbage'));
    }

    /**
     * Purge, reindex everything and flush.
     *
     * @throws \Exception
     */
    private function reindex(): void
    {
        $connection = $this->getServiceContainer()->get('ezpublish.api.storage_engine.legacy.connection');
        $engine = $this->getServiceContainer()->get('cabbage.engine');
        $persistenceHandler = $this->getServiceContainer()->get('ezpublish.spi.persistence.legacy');

        assert($connection instanceof Connection);
        assert($engine instanceof Engine);
        assert($persistenceHandler instanceof PersistenceHandler);

        $queryBuilder = $connection->createQueryBuilder();
        $queryBuilder->select('id', 'current_version')->from('ezcontentobject');
        $statement = $queryBuilder->execute();

        $contentItems = [];

        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            $contentItems[] = $persistenceHandler->contentHandler()->load(
                $row['id'],
                $row['current_version']
            );
        }

        $engine->purgeIndex();
        $engine->bulkIndexContent($contentItems);
        $engine->refresh();
    }
}
