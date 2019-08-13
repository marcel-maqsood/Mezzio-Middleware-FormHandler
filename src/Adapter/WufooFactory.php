<?php


namespace depa\FormularHandlerMiddleware\Adapter;

use PDO;
use Psr\Container\ContainerInterface;

class WufooFactory
{
    /**
     * @throws Exception\InvalidConfigException
     */
    public function __invoke(ContainerInterface $container) : Wufoo
    {
        $pdo = $container->get('config')['???']['wufoo'] ?? null;

        return new Wufoo(
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