<?php


namespace depa\FormularHandlerMiddleware;


use Psr\Http\Message\ResponseInterface;

abstract class AbstractAdapter implements AdapterInterface
{
    protected $formularObj;
    protected $config;
    protected $templateVariables;

    public function __construct(array $requestData, $formularObj)
    {
        $this->formularObj = $formularObj;
        $this->config = $formularObj->getConfig();

        //es gibt in der config 2 bereiche. bereich a definiert die formularelemente, bereich b definiert den/die adapter
        $ret = $this->checkConfig($this->config);
        if ($ret instanceof ResponseInterface){
            return $ret;
        }

        return $this->handleData();
    }



    abstract protected function checkConfig($config);

    abstract public function handleData():ResponseInterface;
}