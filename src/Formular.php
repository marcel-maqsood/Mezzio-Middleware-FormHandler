<?php


namespace depa\FormularHandlerMiddleware;


use depa\FormularHandlerMiddleware\Adapter\SmtpMail;
use depa\FormularHandlerMiddleware\Adapter\PhpMail;
use depa\FormularHandlerMiddleware\Adapter\PdoDatabase;
use depa\FormularHandlerMiddleware\Adapter\Wufoo;

/**
 * Class Formular
 * @package depa\FormularHandlerMiddleware
 */
class Formular
{
    /**
     * @var array
     */
    private $config;

    /**
     * @var bool
     */
    private $errorStatus = false;

    /**
     * @var string
     */
    private $errorDescription = '';

    /**
     * @var array
     */
    private $requestData;

    /**
     *
     * @var array
     */
    private $validFields;

    //Es macht keinen Sinn, immer wieder das selbe zu definieren (FormularHandlerMiddleware...), constanten in eigene Klasse auslagern, die man über container abruft?
    const STATUS_MISSING_VALUE = 'MISSING_VALUE';

    public function __construct(array $config = [], array $requestData = [])
    {
        $this->config = $config;
        // Aufteilen der Config in Berech mit Formularfaledern und bereich mit  adaptern
        $this->requestData = $requestData;
    }

    /**
     * Gibt die im Formular-Objekt gespeicherten Fomular-Daten zurück.
     * @return array
     */
    public function getRequestData(): array
    {
        return $this->requestData;
    }

    /**
     * Setzt die Formular-Daten im Formular-Objekt
     * @param array $data
     */
    public function setRequestData(array $data)
    {
        $this->requestData = $data;
    }

    /**
     * Gibt die im Formular-Objekt gespeicherte Array-Config des Formulars zurück
     * @return array
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Setzt die Array-Config des Formulars im Formular-Objekt
     * @param array $data
     */
    public function setConfig(array $data)
    {
        $this->config = $data;
    }

    /**
     *Prüft, ob "fields" in der Aray-Config des Formulars definiert wurde.
     */
    public function checkFormConfigFields()
    {
        if (!isset($this->getConfig()['fields']) || is_null($this->getConfig()['fields'] || !is_array($this->getConfig()['fields']))) {
            $this->setError("No fields defined in Formular-Config.");
        }
    }

    /**
     * Setzt einen Error mit Beschreibung.
     * @param $errorDescription
     */
    private function setError($errorDescription)
    {
        $this->errorStatus = true;
        $this->errorDescription = $errorDescription;
    }

    /**
     * Gibt ein Boolean zurück, der aussagt ob es ein Fehler gab oder nicht.
     * @return bool
     */
    public function getErrorStatus()
    {
        return $this->errorStatus;
    }

    /**
     *
     * @return string
     */
    public function getErrorDescription()
    {
        return $this->errorDescription;
    }

    /**
     * Gibt die in der Array-Config des Formulars definierten Felder zurück.
     * @return mixed
     */
    public function getFormConfigFields()
    {
        return $this->getConfig()['fields'];
    }

    /**
     * Gibt den in der Array-Config des Formulars definierten Adapter zurück.
     * @return mixed
     */
    public function getFormConfigAdapter()
    {
        return $this->getConfig()['adapter'];
    }

    /**
     * Prüft, ob die Daten des Request alle Felder beinhalten,
     * die in der Array-Config des Formulars als "required" definiert wurden.
     */
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

    /**
     * Gibt die Felder zurück, die in der validateRequestData funktion als vorhanden in der Array-Config gespeichert wurden.
     * @return array
     */
    public function getValidFields(){
        return $this->validFields;
    }

    /**
     * Erstellt einen Treiber, auf welcher Basis die Daten des Formulars abgespeichert/versendet werden.
     * @return SmtpMail|PdoDatabase|Wufoo|null
     */
    public function createDriver(){

        $driverName = strtolower(key($this->getFormConfigAdapter()));

        $driver = null;


        switch ($driverName){
            case 'smtpmail':
                $driver = new SmtpMail($this->config, $this->validFields);
                break;
            case 'phpmail':
                $driver = new PhpMail($this->config, $this->validFields);
                break;
            case 'pdo':
                $driver = new PdoDatabase($this->config, $this->validFields);
                break;
            case 'wufoo':
                $driver = new Wufoo($this->config, $this->validFields);
                break;
        }
        return $driver;
    }

}