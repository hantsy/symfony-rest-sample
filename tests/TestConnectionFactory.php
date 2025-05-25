<?php

namespace App\Tests;

use Doctrine\Bundle\DoctrineBundle\ConnectionFactory;
use Doctrine\Common\EventManager;
use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Tools\DsnParser;
use Testcontainers\Modules\PostgresContainer;

class TestConnectionFactory extends ConnectionFactory
{
    static string $testDsn;

    public function __construct(array $typesConfig, ?DsnParser $dsnParser = null)
    {
        if (!$this::$testDsn) {
            $psql = new PostgresContainer('16');
            $psql->withPostgresDatabase('testdb');
            $psql->withPostgresUser('user');
            $psql->withPostgresPassword('password');
            $psql->withExposedPorts('5432');
            $psql->start();

            $this::$testDsn = sprintf('postgresql://user:password@%s:'.$psql->getFirstMappedPort().'/testdb?serverVersion=16&charset=utf8', $psql->getHost());
        }
        parent::__construct($typesConfig, $dsnParser);
    }


    public function createConnection(array $params, ?Configuration $config = null, ?EventManager $eventManager = null, array $mappingTypes = []): Connection
    {
        $params['url'] = $this::$testDsn;
        return parent::createConnection($params, $config, $eventManager, $mappingTypes);
    }

}