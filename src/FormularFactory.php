<?php

declare(strict_types=1);

namespace depa\FormularHandlerMiddleware;

use Psr\Container\ContainerInterface;

class FormularFactory
{

    public function __invoke(ContainerInterface $container) : Formular
    {

            return new Formular();

    }
}