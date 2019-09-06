<?php


namespace depa\FormularHandlerMiddleware;


use Psr\Http\Message\ResponseInterface;

abstract class AbstractAdapter implements AdapterInterface
{
    protected $errorStatus = false;

    protected $errorDescription = '';

    protected $config;

    protected $validFields;

    protected $eventName;

    public function __construct(array $config = [], array $validFields = [], $eventName = NULL)
    {
        //es gibt in der config 2 bereiche. bereich a definiert die formularelemente, bereich b definiert den/die adapter
        $this->config = $config;
        $this->validFields = $validFields;
        //Subject ist ein extra Feld im Formular, das nicht zwingend sein muss, falls es aber definiert wurde,
        //kann der Inhalt in der Betreffzeile der Form-Config abgerufen werden.
        $this->eventName = $eventName;


        $this->checkConfig($this->config);
        if (!$this->errorStatus){
            return $this->handleData();
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

    abstract protected function checkConfig($config);

    abstract public function handleData();
}