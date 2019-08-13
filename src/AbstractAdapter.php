<?php


namespace depa\FormularHandlerMiddleware;


use Psr\Http\Message\ResponseInterface;

abstract class AbstractAdapter implements AdapterInterface
{
    public function __construct(array $config, array $requestData)
    {
        //es gibt in der config 2 bereiche. bereich a definiert die formularelemente, bereich b definiert den/die adapter
        $ret = $this->checkConfig($config);
        if ($ret instanceof ResponseInterface){
            return $ret;
        }
        $this->checkRequestData($requestData);
        if ($ret instanceof ResponseInterface){
            return $ret;
        }
        return $this->handleData();
    }

    protected function checkRequestData($requestData)
    {

    }

    abstract protected function checkConfig($config);

    abstract public function handleData():ResponseInterface;
}