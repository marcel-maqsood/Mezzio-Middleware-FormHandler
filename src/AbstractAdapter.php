<?php


namespace depa\FormularHandlerMiddleware;


abstract class AbstractAdapter implements AdapterInterface
{
    public function __construct(array $config, array $requestData)
    {
        //es gibt in der config 2 bereiche. bereich a definiert die formularelemente, bereich b definiert den/die adapter
        $this->checkConfig($config);
        $this->checkRequestData($requestData);
        $this->handleData();
    }

    protected function checkRequestData($requestData)
    {

    }

    abstract protected function checkConfig($config);

    abstract public function handleData();
}