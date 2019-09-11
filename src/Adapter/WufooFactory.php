<?php

namespace depa\FormularHandlerMiddleware\Adapter;

use depa\FormularHandlerMiddleware\Formular;
use PDO;
use Psr\Container\ContainerInterface;

/**
 * Class WufooFactory.
 */
class WufooFactory
{
    /**
     * @param ContainerInterface $container
     *
     * @return Wufoo
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
