<?php


namespace depa\FormularHandlerMiddleware;

/**
 * Class AbstractAdapter
 * @package depa\FormularHandlerMiddleware
 */
abstract class AbstractAdapter implements AdapterInterface
{
    /**
     * @var bool
     */
    protected $errorStatus = false;

    /**
     * @var string
     */
    protected $errorDescription = '';

    /**
     * @var array
     */
    protected $config;

    /**
     * @var array
     */
    protected $validFields;

    /**
     * AbstractAdapter constructor.
     * @param array $config
     * @param array $validFields
     */
    public function __construct(array $config = [], array $validFields = [])
    {
        //es gibt in der config 2 bereiche. bereich a definiert die formularelemente, bereich b definiert den/die adapter
        $this->config = $config;
        $this->validFields = $validFields;
        //Subject ist ein extra Feld im Formular, das nicht zwingend sein muss, falls es aber definiert wurde,
        //kann der Inhalt in der Betreffzeile der Form-Config abgerufen werden.

        $this->checkConfig($this->config);
        if (!$this->errorStatus){
            $this->handleData();
        }
    }

    /**
     * Setzt einen Error mit Beschreibung.
     * @param $errorDescription
     */
    protected function setError($errorDescription)
    {
        $this->errorStatus = true;
        $this->errorDescription = $errorDescription;
    }

    /**
     * Gibt den Status des Objekts zurück
     * @return bool
     */
    public function getErrorStatus()
    {
        return $this->errorStatus;
    }

    /**
     * Gibt die Fehlerbeschreibung des Objekts zurück.
     * @return string
     */
    public function getErrorDescription()
    {
        return $this->errorDescription;
    }

    /**
     * Überprüft das übergebene Config-Array.
     * @param $config
     * @return mixed
     */
    abstract protected function checkConfig($config);

    /**
     * Verarbeitet die Daten des Adapters.
     */
    abstract public function handleData();
}