<?php

declare(strict_types=1);

namespace depa\FormularHandlerMiddleware;

use Psr\Container\ContainerInterface;

/**
 * Class FormularFactory
 * @package depa\FormularHandlerMiddleware
 */
class FormularFactory
{

    /**
     * @param ContainerInterface $container
     * @return Formular
     */
    public function __invoke(ContainerInterface $container) : Formular
    {

            return new Formular();

    }
}