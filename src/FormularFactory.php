<?php

declare(strict_types=1);

namespace MazeDEV\FormularHandlerMiddleware;

use Mezzio\Template\TemplateRendererInterface;
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
        $renderer = $container->has(TemplateRendererInterface::class)
            ? $container->get(TemplateRendererInterface::class)
            : null;


        return new Formular($renderer);
    }
}
