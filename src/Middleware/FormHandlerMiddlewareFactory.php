<?php

declare(strict_types=1);

namespace depa\FormHandlerMiddleware\Middleware;

use Psr\Container\ContainerInterface;
use Zend\Expressive\Template\TemplateRendererInterface;

class FormHandlerMiddlewareFactory
{
    public function __invoke(ContainerInterface $container) : FormHandlerMiddleware
    {
        $formConfig = $container->get('config')['depaForm'];
        $problem_details = $container->get(\Zend\ProblemDetails\ProblemDetailsResponseFactory::class);
        return new FormHandlerMiddleware($container->get(TemplateRendererInterface::class), $formConfig, $problem_details);
    }
}
