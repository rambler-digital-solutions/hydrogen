<?php
/**
 * This file is part of Hydrogen package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace RDS\Hydrogen\Tests\Application;

use Doctrine\Common\Cache\FilesystemCache;
use Doctrine\DBAL\Logging\DebugStack;
use Doctrine\DBAL\Logging\SQLLogger;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Setup;
use RDS\Hydrogen\Tests\TestCase;

/**
 * Class DatabaseTestCase
 */
abstract class DatabaseTestCase extends TestCase
{
    /**
     * @var string[]
     */
    protected const ENTITIES = [
        __DIR__ . '/Mock/Entity',
    ];

    /**
     * @var string
     */
    protected const DATABASE_PATH = __DIR__ . '/../resources/db.sqlite';

    /**
     * @var bool
     */
    protected const IS_DEV = true;

    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * @var SQLLogger|DebugStack
     */
    protected $logger;

    /**
     * @return void
     * @throws \Doctrine\ORM\ORMException
     * @throws \InvalidArgumentException
     * @throws \Doctrine\DBAL\DBALException
     * @throws \Doctrine\Common\Annotations\AnnotationException
     */
    public function setUp(): void
    {
        $this->logger          = new DebugStack();
        $this->logger->enabled = false;

        $this->createEmptyDatabase();

        $connection = [
            'driver' => 'pdo_sqlite',
            'path'   => static::DATABASE_PATH,
        ];

        $this->em = EntityManager::create($connection, $this->createConfiguration($this->logger));

        $this->createDefaultDatabaseStructure($this->em);
    }

    /**
     * @return void
     */
    private function createEmptyDatabase(): void
    {
        \fclose(\fopen(static::DATABASE_PATH, 'wb+'));
    }

    /**
     * @param SQLLogger $logger
     * @return Configuration
     * @throws \Doctrine\Common\Annotations\AnnotationException
     */
    private function createConfiguration(SQLLogger $logger): Configuration
    {
        $config = Setup::createAnnotationMetadataConfiguration(
            static::ENTITIES,
            static::IS_DEV,
            __DIR__ . '/../.temp',
            new FilesystemCache(__DIR__ . '/../.temp'),
            false
        );

        $config->setSQLLogger($logger);

        return $config;
    }

    /**
     * @param EntityManagerInterface $em
     * @throws \Doctrine\DBAL\DBALException
     */
    private function createDefaultDatabaseStructure(EntityManagerInterface $em): void
    {
        $connection = $em->getConnection();
        $connection->exec(\file_get_contents(__DIR__ . '/../resources/up.sql'));
    }

    /**
     * @return void
     */
    public function tearDown(): void
    {
        $this->em->close();
    }

    /**
     * @param \Closure $queries
     * @return array
     */
    protected function log(\Closure $queries): array
    {
        $this->logger->enabled = true;
        $queries();
        $this->logger->enabled = false;

        [$result, $this->logger->queries] = [$this->logger->queries, []];

        return \array_map(function (array $data): string {
            return $data['sql'];
        }, $result);
    }
}
