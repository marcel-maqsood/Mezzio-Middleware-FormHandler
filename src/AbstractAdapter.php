<?php

namespace MazeDEV\FormularHandlerMiddleware;


use Mezzio\Template\TemplateRendererInterface;
/**
 * Class AbstractAdapter.
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

    protected $submitEmail;

    protected $adapter;

    protected $renderer;

    /**
     * AbstractAdapter constructor.
     *
     * @param array $config
     * @param array $validFields
     */
    public function __construct(array $config, array $adapter,  array $validFields = [], TemplateRendererInterface $renderer = null)
    {
        $this->config = $config;
        $this->adapter = $adapter;
        $this->validFields = $validFields;
        $this->renderer = $renderer;

        $this->submitEmail = $this->recursiveFindEmailField($config['fields'], $validFields);

        $this->checkConfig($adapter);
        if (!$this->errorStatus) 
        {
            $this->handleData();
        }
    }

    /**
     * Setzt einen Error mit Beschreibung.
     *
     * @param $errorDescription
     */
    protected function setError($errorDescription)
    {
        $this->errorStatus = true;
        $this->errorDescription = $errorDescription;
    }

    /**
     * Gibt den Status des Objekts zurück.
     *
     * @return bool
     */
    public function getErrorStatus()
    {
        return $this->errorStatus;
    }

    /**
     * Gibt die Fehlerbeschreibung des Objekts zurück.
     *
     * @return string
     */
    public function getErrorDescription()
    {
        return $this->errorDescription;
    }

    /**
     * Überprüft das übergebene Config-Array.
     *
     * @param $config
     *
     * @return mixed
     */
    abstract protected function checkConfig($config);

    /**
     * Verarbeitet die Daten des Adapters.
     */
    abstract public function handleData();

    protected function recursiveFindEmailField($fields, $validFields) : string | null
    {
        foreach ($fields as $key => $field) 
        {
            if(!isset($validFields[$key]))
            {
                continue;
            }
            
            if($field['type'] == 'array')
            {
                $email = $this->recursiveFindEmailField($field['childs'], $validFields[$key]);
                if($email != null)
                {
                    return $email;
                }
            }
            
            if (isset($field['type']) && $field['type'] == 'email') 
            {
                return $validFields[$key];
            }
        }
        return null;
    }
}
