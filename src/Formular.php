<?php


namespace depa\FormularHandlerMiddleware;


use depa\FormularHandlerMiddleware\Adapter\Mail;
use depa\FormularHandlerMiddleware\Adapter\PdoDatabase;
use depa\FormularHandlerMiddleware\Adapter\Wufoo;

class Formular
{
    /**
     * @var array
     */
    private $config;

    private $errorStatus = false;

    private $errorDescription = '';

    /**
     * @var array
     */
    private $requestData;

    private $problemDetails;

    private $validFields;

    //Es macht keinen Sinn, immer wieder das selbe zu definieren (FormularHandlerMiddleware...), constanten in eigene Klasse auslagern, die man über container abruft?
    const STATUS_MISSING_VALUE = 'MISSING_VALUE';

    public function __construct(array $config = [], array $requestData = [])
    {
        $this->config = $config;
        // Aufteilen der Config in Berech mit Formularfaledern und bereich mit  adaptern
        $this->requestData = $requestData;
    }

    public function getRequestData(): array
    {
        return $this->requestData;
    }

    public function setRequestData(array $data)
    {
        $this->requestData = $data;
    }

    public function getConfig()
    {
        return $this->config;
    }

    public function setConfig(array $data)
    {
        $this->config = $data;
    }

    public function checkFormConfigFields()
    {
        if (!isset($this->getConfig()['fields']) || is_null($this->getConfig()['fields'] || !is_array($this->getConfig()['fields']))) {
            return $this->problemDetails->createResponse(
                $this->request,
                400,
                "No fields defined in Formular-Config.",
                self::STATUS_MISSING_VALUE,
                "N/A"
            );
        }
    }

    private function setError($errorDescription)
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

    public function getFormConfigFields()
    {
        return $this->getConfig()['fields'];
    }

    public function getFormConfigAdapter()
    {
        return $this->getConfig()['adapter'];
    }

    public function validateRequestData()
    {

        if(!isset($this->config['fields']) || !is_array($this->config['fields'])){
            $this->setError('The Form-Config is missing a definition for fields!');
        }else{
            $validFields = null;
            foreach ($this->config['fields'] as $field => $fieldEntry) {
                if (isset($fieldEntry['required']) && $fieldEntry['required'] == true) {
                    if (!isset($this->requestData[$field])) {
                        $this->setError('The field ' . $field . ' was not found in the submitted form!');
                    }
                }

                if (isset($this->requestData[$field])) {
                    $validFields[$field] = $this->requestData[$field];
                }
            }
            $this->validFields = $validFields;
        }

    }

    public function getValidFields(){
        return $this->validFields;
    }

    public function createDriver(){

        $driverName = strtolower(key($this->getFormConfigAdapter()));

        $driver = null;
        switch ($driverName){

            case 'mail':
                $driver = new Mail($this);
                break;
            case 'pdo':
                $driver = new PdoDatabase($this);
                break;
            case 'wufoo':
                $driver = new Wufoo($this);
                break;
        }
        return $driver;
    }

}