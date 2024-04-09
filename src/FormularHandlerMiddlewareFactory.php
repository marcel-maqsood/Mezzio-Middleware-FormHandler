<?php

declare(strict_types=1);

namespace MazeDEV\FormularHandlerMiddleware;

use Psr\Container\ContainerInterface;
use Mezzio\ProblemDetails\ProblemDetailsResponseFactory;

/**
 * Class FormularHandlerMiddlewareFactory.
 */
class FormularHandlerMiddlewareFactory
{
    /**
     * @param ContainerInterface $container
     *
     * @return FormularHandlerMiddleware
     */
    public function __invoke(ContainerInterface $container) : FormularHandlerMiddleware
    {
        /**
         * aus meiner Sicht müsste hier ein Formular-Objekt generiert werden.
         * dieses erhält die config-daten und die post-daten
         * entsprechend der config-daten wird der dort definierte adapter geladen.
         * ist keiner definiert oder gibt es den definierten adapter nicht >> Exception???
         *
         * nun wird über den adapter geprüft, ob alle vom adapter benötigten config-daten vorhanden sind
         * nun werden im form-objekt die formulardaten überprüft
         *
         * ist alles ok, werden die formdaten je nach adapter "gesendet"
         */
        $formConfig = $container->get('config')['mazeform'];
        $formularObj = $container->get(Formular::class);
        $problemDetails = $container->get(ProblemDetailsResponseFactory::class);

        return new FormularHandlerMiddleware($formConfig, $formularObj, $problemDetails);
    }
}
