<?php

declare(strict_types=1);

namespace depa\FormularHandlerMiddleware;

use Psr\Container\ContainerInterface;
use Zend\ProblemDetails\ProblemDetailsResponseFactory;

class FormularHandlerMiddlewareFactory
{
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

        $formConfig = $container->get('config')['depaForm'];
        $formularObj = $container->get(Formular::class);
        $problemDetails = $container->get(ProblemDetailsResponseFactory::class);
        return new FormularHandlerMiddleware( $formConfig, $formularObj, $problemDetails);
    }
}
