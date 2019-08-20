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
            'dependencies' => $this->getDependencies(),
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
                Formular::class => FormularFactory::class,
            ],
        ];
    }
}