<?php

declare(strict_types=1);

namespace Cabbage\Tests\Integration\API;

use function assert;
use Cabbage\Core\Engine;
use Doctrine\DBAL\Connection;
use eZ\Publish\API\Repository\Repository;
use eZ\Publish\API\Repository\Tests\SetupFactory\Legacy as CoreSetupFactory;
use eZ\Publish\Core\Base\Container\Compiler;
use eZ\Publish\Core\Base\ServiceContainer;
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
     * @throws \Exception
     */
    public function getServiceContainer(): ServiceContainer
    {
        if (self::$serviceContainer === null) {
            self::$serviceContainer = $this->buildServiceContainer();
        }

        return self::$serviceContainer;
    }

    /**
     * @throws \Exception
     */
    private function buildServiceContainer(): ServiceContainer
    {
        $config = $this->getConfig();

        // Needed by the ContainerBuilder included below
        $installDir = $config['install_dir'];

        /** @var \Symfony\Component\DependencyInjection\ContainerBuilder $containerBuilder */
        $containerBuilder = include $config['container_builder_path'];
        $containerBuilder->setParameter('legacy_dsn', self::$dsn);
        $containerBuilder->setParameter(
            'io_root_dir',
            self::$ioRootDir . '/' . $containerBuilder->getParameter('storage_dir')
        );

        $this->localBuildContainer($containerBuilder);

        return new ServiceContainer(
            $containerBuilder,
            $installDir,
            $config['cache_dir'],
            true,
            true
        );
    }

    /**
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $containerBuilder
     *
     * @throws \Exception
     */
    private function localBuildContainer(ContainerBuilder $containerBuilder): void
    {
        $settingsPath = __DIR__ . '/../../../config/symfony/';

        $solrLoader = new YamlFileLoader($containerBuilder, new FileLocator($settingsPath));
        $solrLoader->load('services.yml');

        $containerBuilder->addCompilerPass(new Compiler\Search\SearchEngineSignalSlotPass('cabbage'));
    }

    private function getConfig(): array
    {
        return include __DIR__ . '/../../../vendor/ezsystems/ezpublish-kernel/config.php-DEVELOPMENT';
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
