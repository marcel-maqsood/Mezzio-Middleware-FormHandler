<?php


namespace depa\FormularHandlerMiddleware;


class Formular
{
    /**
     * @var array
     */
    private $config;

    /**
     * @var array
     */
    private $requestData;

    public function __construct(array $config = [], array $requestData = [])
    {
        $this->config = $config;
        // Aufteilen der Config in Berech mit Formularfaledern und bereich mit  adaptern
        $this->requestData = $requestData;
    }

    public function getRequestData() : array
    {
        return $this->requestData;
    }
}