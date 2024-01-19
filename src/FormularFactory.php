<?php

declare(strict_types=1);

namespace ElectricBrands\FormularHandlerMiddleware;

use Psr\Container\ContainerInterface;

/**
 * Class FormularFactory.
 */
class FormularFactory
{
    /**
     * @param ContainerInterface $container
     *
     * @return Formular
     */
    public function __invoke(ContainerInterface $container) : Formular
    {
        return new Formular();
    }
}
