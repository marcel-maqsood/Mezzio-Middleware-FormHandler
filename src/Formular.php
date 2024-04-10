<?php

namespace MazeDEV\FormularHandlerMiddleware;

use MazeDEV\FormularHandlerMiddleware\Adapter\PdoDatabase;
use MazeDEV\FormularHandlerMiddleware\Adapter\PhpMail;
use MazeDEV\FormularHandlerMiddleware\Adapter\SmtpMail;
use MazeDEV\FormularHandlerMiddleware\Adapter\Wufoo;

/**
 * Class Formular.
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
     * @var array
     */
    private $validFields;

    /**
     * Gibt die im Formular-Objekt gespeicherten Fomular-Daten zurück.
     *
     * @return array
     */
    public function getRequestData(): array
    {
        return $this->requestData;
    }

    /**
     * Setzt die Formular-Daten im Formular-Objekt.
     *
     * @param array $data
     */
    public function setRequestData(array $data)
    {
        $this->requestData = $data;
    }

    /**
     * Gibt die im Formular-Objekt gespeicherte Array-Config des Formulars zurück.
     *
     * @return array
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Setzt die Array-Config des Formulars im Formular-Objekt.
     *
     * @param array $data
     */
    public function setConfig(array $data)
    {
        $this->config = $data;
    }

    /**
     *Prüft, ob "fields" in der Array-Config des Formulars definiert wurde.
     */
    public function checkFormConfigFields()
    {
        if (!isset($this->getConfig()['fields']) || is_null($this->getConfig()['fields'] || !is_array($this->getConfig()['fields']))) 
        {
            $this->setError('No fields defined in Formular-Config.');
        }
    }

    /**
     * Setzt einen Error mit Beschreibung.
     *
     * @param $errorDescription
     */
    private function setError($errorDescription)
    {
        $this->errorStatus = true;
        $this->errorDescription = $errorDescription;
    }

    /**
     * Gibt ein Boolean zurück, der aussagt ob es ein Fehler gab oder nicht.
     *
     * @return bool
     */
    public function getErrorStatus()
    {
        return $this->errorStatus;
    }

    /**
     * Gibt die Beschreibung des Fehler zurück.
     *
     * @return string
     */
    public function getErrorDescription()
    {
        return $this->errorDescription;
    }

    /**
     * Gibt die in der Array-Config des Formulars definierten Felder zurück.
     *
     * @return mixed
     */
    public function getFormConfigFields()
    {
        return $this->getConfig()['fields'];
    }

    /**
     * Gibt den in der Array-Config des Formulars definierten Adapter zurück.
     *
     * @return mixed
     */
    public function getFormConfigAdapters()
    {
        return $this->getConfig()['adapters'];
    }

    /**
     * Prüft, ob die Daten des Request alle Felder beinhalten,
     * die in der Array-Config des Formulars als "required" definiert wurden.
     */
    public function validateRequestData()
    {
        if (!isset($this->config['fields']) || !is_array($this->config['fields'])) 
        {
            $this->setError('The Form-Config is missing a definition for fields!');
            return;
        }

        $this->validFields = $this->getValidatedFields($this->requestData, $this->config['fields']);
    }

    private function getValidatedFields($data, $fields) 
    {
        $validFields = null;
    
        foreach ($fields as $fieldName => $fieldEntry) 
        {
            if (!isset($data[$fieldName])) 
            {
                if (!isset($fieldEntry['required']) || $fieldEntry['required'] == false) 
                {
                    continue;
                }
                return "Das Feld '{$fieldName}' wurde im übergebenen Array nicht gefunden!";
            }
    
            if ($fieldEntry['type'] === 'array' && isset($fieldEntry['childs'])) 
            {
                $childValidation = $this->getValidatedFields($data[$fieldName], $fieldEntry['childs']);
                if ($childValidation !== null) 
                {
                    $validFields[$fieldName] = $childValidation;
                    continue;
                }
            }
            $validFields[$fieldName] = $data[$fieldName];
        }
    
        return $validFields;
    }

    /**
     * Gibt die Felder zurück, die in der validateRequestData funktion als vorhanden in der Array-Config gespeichert wurden.
     *
     * @return array
     */
    public function getValidFields()
    {
        return $this->validFields;
    }

    /**
     * Erstellt einen Treiber, auf welcher Basis die Daten des Formulars abgespeichert/versendet werden.
     *
     * @return array|null
     */
    public function createDrivers()
    {
        $drivers = [];

        $configAdapters = $this->getFormConfigAdapters();

        if($configAdapters == null)
        {
            return [null];
        }

        foreach($configAdapters as $adapter)
        {
            if($adapter == null)
            {
                $drivers[] = null;
                continue;
            }

            switch ($adapter['method']) 
            {
                case 'smtpmail':
                    $drivers[] = new SmtpMail($this->config, $adapter, $this->validFields);
                    break;
                case 'phpmail':
                    $drivers[] = new PhpMail($this->config, $adapter, $this->validFields);
                    break;
                case 'pdo':
                    $drivers[] = new PdoDatabase($this->config, $adapter, $this->validFields);
                    break;
                case 'wufoo':
                    $drivers[] = new Wufoo($this->config, $adapter, $this->validFields);
                    break;
            }
        }

        if(count($drivers) == 0)
        {
            $drivers[] = null;
        }

        return $drivers;
    }
}
