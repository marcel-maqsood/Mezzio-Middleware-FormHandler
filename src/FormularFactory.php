<?php

declare(strict_types=1);

namespace depa\FormularHandlerMiddleware;


use Zend\ProblemDetails\ProblemDetailsResponseFactory;

class FormularFactory
{

    private $container;

    public function __invoke(ContainerInterface $container) : callable
    {
        $this->container = $container;

            return new Formular();

    }
}