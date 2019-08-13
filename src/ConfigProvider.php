<?php

declare(strict_types=1);

namespace depa\FormularHandlerMiddleware;


class ConfigProvider
{
    /**
     * Return the configuration array.
     */
    public function __invoke() : array
    {
        return [
            'authentication' => $this->getFormularConfig(),
            'dependencies' => $this->getDependencies(),
        ];
    }

    public function getFormularConfig() : array
    {
        return [
            /*
             * Hier muss die config geladen werden
             */
        ];
    }

    /**
     * Returns the container dependencies
     */
    public function getDependencies() : array
    {
        return [
            'aliases' => [

            ],
            'factories' => [
                FormularHandlerMiddleware::class => FormularHandlerMiddlewareFactory::class,
                Adapter\PdoDatabase\PdoDatabase::class => Adapter\PdoDatabase\PdoDatabaseFactory::class,
                Adapter\Wufoo\Wufoo::class => Adapter\Wufoo\WufooFactory::class,
                Adapter\Mail\Mail::class => Adapter\Mail\MailFactory::class,
                Formular::class => FormularFactory::class,
            ],
        ];
    }
}