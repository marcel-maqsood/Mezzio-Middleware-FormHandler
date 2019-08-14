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

    private $problemDetails;

    //Es macht keinen Sinn, immer wieder das selbe zu definieren (FormularHandlerMiddleware...), constanten in eigene Klasse auslagern, die man über container abruft?
    const STATUS_MISSING_VALUE = 'MISSING_VALUE';

    public function __construct(array $config = [], array $requestData = [], $problemDetails)
    {
        $this->config = $config;
        // Aufteilen der Config in Berech mit Formularfaledern und bereich mit  adaptern
        $this->requestData = $requestData;

        $this->problemDetails = $problemDetails;
    }

    public function getRequestData() : array
    {
        return $this->requestData;
    }

    public function setRequestData(array $data){
        $this->requestData = $data;
    }

    public function getConfig(){
        return $this->config;
    }

    public function setConfig(array $data){
        $this->config = $data;
    }

    public function checkFormConfigFields(){
        if(!isset($this->getConfig()['fields']) || is_null($this->getConfig()['fields'] || !is_array($this->getConfig()['fields']))){
            return $this->problemDetails->createResponse(
                $this->request,
                400,
                "No fields defined in Formular-Config.",
                self::STATUS_MISSING_VALUE,
                "N/A"
            );
        }
    }

    public function getFormConfigFields(){
        return $this->getConfig()['fields'];
    }

    public function getFormConfigAdapter(){
        return $this->getConfig()['adapter'];
    }

}