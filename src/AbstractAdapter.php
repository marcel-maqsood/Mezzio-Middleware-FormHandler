<?php


namespace depa\FormularHandlerMiddleware;


use Psr\Http\Message\ResponseInterface;

abstract class AbstractAdapter implements AdapterInterface
{
    protected $errorStatus = false;

    protected $errorDescription = '';

    protected $config;

    protected $validFields;

    public function __construct(array $config = [], array $validFields = [])
    {
        $this->config = $config;
        $this->validFields = $validFields;
        //es gibt in der config 2 bereiche. bereich a definiert die formularelemente, bereich b definiert den/die adapter
        $ret = $this->checkConfig($this->config);
        if (is_null($ret)){
            return $ret;
        }
        return $this->handleData();
    }

    public function setError($errorDescription)
    {
        $this->errorStatus = true;
        $this->errorDescription = $errorDescription;
    }

    public function getErrorStatus()
    {
        return $this->errorStatus;
    }

    public function getErrorDescription()
    {
        return $this->errorDescription;
    }

    abstract protected function checkConfig($config);

    abstract public function handleData():ResponseInterface;
}