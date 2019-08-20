<?php


namespace depa\FormularHandlerMiddleware\Adapter;

use depa\FormularHandlerMiddleware\Formular;
use Psr\Container\ContainerInterface;

class MailFactory
{
    /**
     * @param ContainerInterface $container
     * @return Mail
     */
    public function __invoke(ContainerInterface $container) : Mail
    {
        //$pdo = $container->get('config')['???']['mail'] ?? null;

        return new Mail(
            $container->get(Formular::class)
        );
    }
}