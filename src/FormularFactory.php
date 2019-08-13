<?php

declare(strict_types=1);

namespace depa\FormularHandlerMiddleware;


class FormularFactory
{
    public function __invoke(ContainerInterface $container) : callable
    {
        return function (array $config = [], array $requestData = []) : Formular {
            return new Formular($config, $requestData);
        };
    }
}