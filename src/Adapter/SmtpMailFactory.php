<?php


namespace depa\FormularHandlerMiddleware\Adapter;

use depa\FormularHandlerMiddleware\Formular;
use Psr\Container\ContainerInterface;

class SmtpMailFactory
{
    /**
     * @param ContainerInterface $container
     * @return SmtpMail
     */
    public function __invoke(ContainerInterface $container) : SmtpMail
    {
        //$pdo = $container->get('config')['???']['mail'] ?? null;

        return new SmtpMail(
            $container->get(Formular::class)
        );
    }
}