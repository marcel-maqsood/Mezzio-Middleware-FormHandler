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
        $this->checkRequestData($requestData, $this->config);
        if ($ret instanceof ResponseInterface){
            return $ret;
        }
        return $this->handleData();
    }

    protected function checkRequestData($requestData, $config)
    {
        $formData = $requestData['data'];

        $templateVariables = null;
        foreach ($this->config['fields'] as $field => $fieldEntry){
            if(!isset($fieldEntry['required']) && $fieldEntry['required'] == true){
                if(!isset($formData[$field])){
                    //Return mit zend problemDetails? - falls ja, wie kriegt man $request hier rein?
                }
            }

            if (isset($this->config[$field])) {
                $templateVariables[$field] = $formData[$field];
            }
        }
        $this->templateVariables = $templateVariables;
        $this->templateVariables['recipients'] = $this->config['recipients'];

    }

    abstract protected function checkConfig($config);

    abstract public function handleData():ResponseInterface;
}