<?php

declare(strict_types=1);

namespace MazeDEV\FormularHandlerMiddleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Server\MiddlewareInterface;
use Laminas\Diactoros\Response\JsonResponse;
use Laminas\Json\Json;
use Mezzio\ProblemDetails\ProblemDetailsResponseFactory;
use Mezzio\Csrf\CsrfMiddleware;

/**
 * Class FormularHandlerMiddleware.
 */
class FormularHandlerMiddleware implements MiddlewareInterface
{
    /**
     * @const STATUS_MISSING_VALUE
     */
    const STATUS_MISSING_VALUE = 'MISSING_VALUE';

    /**
     * @var array
     */
    private $formDefinition;

    /**
     * @var ProblemDetailsResponseFactory
     */
    private $problemDetails;

    /**
     * @var Formular
     */
    private $formularObj;

    private $guard;

    /**
     * FormularHandlerMiddleware constructor.
     *
     * @param $formDefinition
     * @param Formular                      $formularObj
     * @param ProblemDetailsResponseFactory $problemDetails
     */
    public function __construct($formDefinition, Formular $formularObj, ProblemDetailsResponseFactory $problemDetails)
    {
        $this->formDefinition = $formDefinition;
        $this->problemDetails = $problemDetails;
        $this->formularObj = $formularObj;
    }

    /**
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $request
     *
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler) : ResponseInterface
    {

        if ($request->getMethod() !== 'POST') 
        {
            //Since this request is not a POST, we can't extract any information and thus we'll just pass it.
            return $handler->handle($request);
        }


		if (class_exists('Mezzio\Csrf\CsrfMiddleware'))
		{
			$this->guard = $request->getAttribute(CsrfMiddleware::GUARD_ATTRIBUTE);
		}

        $dataArray = null;
        $asJson = true;
        $asArray = true;

        try 
        {
            $dataArray = Json::decode($request->getBody()->getContents(), Json::TYPE_ARRAY);
        }
        catch (\Exception $e)
        {
            $asJson = false;
        }

        if(!$asJson)
        {
            try 
            {
                if(empty($request->getParsedBody()))
                {
                    parse_str($request->getBody()->getContents(), $dataArray);
                }
                else
                {
                    $dataArray = $request->getParsedBody();
                }
            } 
            catch(\Exception $e) 
            {
                $asArray = false;
            }
        }
        
        if(!$asArray && !$asJson)
        {
            return $this->problemDetails->createResponse(
                $request,
                400,
                'Data was missing in submitted form.',
                self::STATUS_MISSING_VALUE,
                'N/A'
            );
        }

        if (is_null($dataArray) || !is_array($dataArray) || !array_key_exists('data', $dataArray)) 
        {
            var_dump($dataArray);
            exit;
            return $this->problemDetails->createResponse(
                $request,
                400,
                'Data was missing in submitted form.',
                self::STATUS_MISSING_VALUE,
                'N/A'
            );
        }

        $formData = $dataArray['data'] ?? null;

        if($formData == null)
        {
            //if there is no data attribute in that request, we pass it down the pipe as it is not meant for our handler.
            return $handler->handle($request);
        }

        if (!array_key_exists('config', $formData)) 
        {
            return $this->problemDetails->createResponse(
                $request,
                500,
                "Der Verweis auf die Formular-Konfiguration fehlt. Das Formular benötigt ein entsprechendes Hidden-Feld mit dem Namen 'config'.",
                self::STATUS_MISSING_VALUE,
                'N/A'
            );
        }

        $formConfig = $this->formDefinition['forms'][$formData['config']];
        $this->formularObj->setConfig($this->makeFormConfig($formConfig));
        
        $this->formularObj->setRequestData($formData);
        $this->formularObj->validateRequestData();

        if($this->formularObj->getFormConfigAdapters() == null)
        {
            //If adapter is set to null, we will pass the form and append the formdata so that the next handler may use it.
            $request = $request->withAttribute('formData', $formData);
            return $handler->handle($request);
        }

        if($this->formularObj->csrf != '')
        {
            if($this->guard != null)
            {
                if (!$this->guard->validateToken($this->formularObj->csrf)) 
                {
                    $request = $request->withAttribute('formData', $formData);
                    $request = $request->withAttribute('csrfError', '...');
                    return $handler->handle($request);
                }
            }
        }

        if ($this->formularObj->getErrorStatus()) 
        {
            return $this->problemDetails->createResponse(
                $request,
                400,
                $this->formularObj->getErrorDescription(),
                self::STATUS_MISSING_VALUE,
                'N/A'
            );
        }

        $dataDrivers = $this->formularObj->createDrivers();
        foreach($dataDrivers as $driver)
        {
            if($driver == null)
            {
                $request = $request->withAttribute('formData', $formData);
                return $handler->handle($request);
            }

            if ($driver->getErrorStatus()) 
            {
                return $this->problemDetails->createResponse(
                    $request,
                    500,
                    $driver->getErrorDescription(),
                    self::STATUS_MISSING_VALUE,
                    'N/A'
                );
            }
        }

        return new JsonResponse(
            'The email was successfully sent!',
            200,
            ['Content-Type' => ['application/json'], 'cache-control' => ['no-cache, must-revalidate']]
        );
    }

    /**
     * Scannt die Config des Formulars nach definierten Adapter-Namen ab,
     * um an stelle des Namen ein tatsächliches config-Array einzubauen.
     *
     * @param $formConfig
     *
     * @return mixed
     */
    private function makeFormConfig($formConfig)
    {
        if (!isset($formConfig['adapters']) || !is_array($formConfig['adapters'])) 
        {
            return $formConfig;
        }

        $adapters = $formConfig['adapters'];
        foreach ($adapters as $key => $value) 
        {
            if (is_string($value)) 
            {
                if (isset($this->formDefinition['adapters']) && array_key_exists($value, $this->formDefinition['adapters'])) 
                {
                    $tempAdapter = $this->formDefinition['adapters'][$value];
                    unset($formConfig['adapters'][$key]);
                    $formConfig['adapters'][] = array_merge($formConfig['adapters'], $tempAdapter);
                }
            }
        }

        return $formConfig;
    }
}
