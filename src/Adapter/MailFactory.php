<?php


namespace depa\FormularHandlerMiddleware\Adapter;

use depa\FormularHandlerMiddleware\FormularFactory;
use PDO;
use Psr\Container\ContainerInterface;

class MailFactory
{
    /**
     * @throws Exception\InvalidConfigException
     */
    public function __invoke(ContainerInterface $container) : Mail
    {
        $pdo = $container->get('config')['???']['mail'] ?? null;

        return new Mail(
            $container->get(FormularFactory::class)
        );
    }
}