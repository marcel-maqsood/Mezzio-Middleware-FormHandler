<?php


namespace depa\FormularHandlerMiddleware;


use Psr\Http\Message\ResponseInterface;

abstract class AbstractAdapter implements AdapterInterface
{
    protected $errorStatus = false;

    protected $errorDescription = '';

    protected $formularObj;

    protected $config;

    public function __construct(Formular $formularObj)
    {
        $this->formularObj = $formularObj;
        $this->config = $formularObj->getConfig();

        //es gibt in der config 2 bereiche. bereich a definiert die formularelemente, bereich b definiert den/die adapter
        $ret = $this->checkConfig($this->config);
        if (is_null($ret)){
            return $ret;
        }
        return $this->handleData($formularObj->getValidFields());
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