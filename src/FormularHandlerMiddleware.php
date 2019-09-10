<?php

declare(strict_types=1);

namespace depa\FormularHandlerMiddleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Zend\Diactoros\Response\JsonResponse;
use Zend\Json\Json;
use Zend\ProblemDetails\ProblemDetailsResponseFactory;


class FormularHandlerMiddleware implements RequestHandlerInterface
{

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

    public function __construct($formDefinition, Formular $formularObj, ProblemDetailsResponseFactory $problemDetails)
    {
        $this->formDefinition = $formDefinition;
        $this->problemDetails = $problemDetails;
        $this->formularObj = $formularObj;
    }

    /**
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $dataArray = Json::decode($request->getBody()->getContents(), Json::TYPE_ARRAY);
        //evtl. überprüfen?

        if (is_null($dataArray) || !is_array($dataArray) || !array_key_exists('data', $dataArray)) {
            return $this->problemDetails->createResponse(
                $request,
                400,
                "Data was missing in submitted form.",
                self::STATUS_MISSING_VALUE,
                "N/A"
            );
        }

        $formData = $dataArray['data'];

        if (!array_key_exists('config', $formData)) {
            return $this->problemDetails->createResponse(
                $request,
                500,
                "Der Verweis auf die Formular-Konfiguration fehlt. Das Formular benötigt ein entsprechendes Hidden-Feld mit dem Namen 'config'.",
                self::STATUS_MISSING_VALUE,
                "N/A"
            );
        }

        $formConfig = $this->formDefinition['forms'][$formData['config']];

        $this->formularObj->setConfig($this->makeFormConfig($formConfig));
        $this->formularObj->setRequestData($formData);
        $this->formularObj->validateRequestData();

        if ($this->formularObj->getErrorStatus()) {
            return $this->problemDetails->createResponse(
                $request,
                400,
                $this->formularObj->getErrorDescription(),
                self::STATUS_MISSING_VALUE,
                "N/A"
            );
        }

        $dataDriver = $this->formularObj->createDriver();
        if($dataDriver->getErrorStatus()){
            return $this->problemDetails->createResponse(
                $request,
                500,
                $dataDriver->getErrorDescription(),
                self::STATUS_MISSING_VALUE,
                'N/A'
            );
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
     * @return mixed
     */
    private function makeFormConfig($formConfig)
    {
        if(!isset($formConfig['adapter']) || !is_array($formConfig['adapter'])){
            return $formConfig;
        }

        $adapter = $formConfig['adapter'];
        foreach ($adapter as $key => $value) {
            if (is_string($value)) {
                if (isset($this->formDefinition['adapter']) && array_key_exists($value, $this->formDefinition['adapter'])) {
                    $tempAdapter = $this->formDefinition['adapter'][$value];
                    unset($formConfig['adapter'][$key]);
                    $formConfig['adapter'] = array_merge($formConfig['adapter'], $tempAdapter);
                }
            }
        }
        return $formConfig;
    }
}
