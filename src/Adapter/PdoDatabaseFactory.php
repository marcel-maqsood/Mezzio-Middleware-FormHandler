<?php

namespace ElectricBrands\FormularHandlerMiddleware\Adapter;

use PDO;
use Psr\Container\ContainerInterface;

/**
 * Class PdoDatabaseFactory.
 */
class PdoDatabaseFactory
{
    /**
     * @param ContainerInterface $container
     *
     * @throws Exception\InvalidConfigException
     *
     * @return PdoDatabase
     */
    public function __invoke(ContainerInterface $container) : PdoDatabase
    {
        $pdo = $container->get('config')['???']['pdo'] ?? null;
        if (null === $pdo) 
        {
            throw new Exception\InvalidConfigException(
                'PDO values are missing in formular config'
            );
        }
        if (!isset($pdo['table'])) 
        {
            throw new Exception\InvalidConfigException(
                'The PDO table name is missing in the configuration'
            );
        }
        if (!isset($pdo['field']['identity'])) 
        {
            throw new Exception\InvalidConfigException(
                'The PDO identity field is missing in the configuration'
            );
        }
        if (!isset($pdo['field']['password'])) 
        {
            throw new Exception\InvalidConfigException(
                'The PDO password field is missing in the configuration'
            );
        }
        if (isset($pdo['service']) && $container->has($pdo['service'])) 
        {
            return new PdoDatabase(
                $container->get($pdo['service']),
                $pdo,
                $container->get(UserInterface::class)
            );
        }
        if (!isset($pdo['dsn'])) 
        {
            throw new Exception\InvalidConfigException(
                'The PDO DSN value is missing in the configuration'
            );
        }

        return new PdoDatabase(
            new PDO(
                $pdo['dsn'],
                $pdo['username'] ?? null,
                $pdo['password'] ?? null
            ),
            $pdo,
            $container->get(Formular::class)
        );
    }
}
