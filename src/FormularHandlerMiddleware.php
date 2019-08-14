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
     * @var TemplateRendererInterface
     */
    private $renderer;

    private $formDefinition;

    private $transport;

    private $problemDetails;

    public function __construct($formDefinition,ProblemDetailsResponseFactory $problemDetails)
    {
        $this->formDefinition = $formDefinition;
        $this->problemDetails = $problemDetails;
    }

    /**
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $dataArray = Json::decode($request->getBody()->getContents(), Json::TYPE_ARRAY);
        //evtl. überprüfen?

        if (is_null($dataArray) || !is_array($dataArray) || !array_key_exists($dataArray['data'])){
            return $this->problemDetails->createResponse(
                $this->request,
                400,
                "Data was missing in submitted form.",
                self::STATUS_MISSING_VALUE,
                "N/A"
            );
        }

        $formData = $dataArray['data'];

        if (!array_key_exists($formData['config'])){
            return $this->problemDetails->createResponse(
                $this->request,
                400,
                "Die Bezeichnung der Config fehlt",
                self::STATUS_MISSING_VALUE,
                "N/A"
            );
        }

        // $formData['config']
        // damit bekommen wir nun die Config für das konkrete Formular

        $formConfig = $this->formDefinition['forms'][$formData['config']];
        $formConfig = $this->makeFormConfig($formConfig);


    }


    private function makeFormConfig($formConfig)
    {
        $adapter = $formConfig['adapter'];

        foreach($adapter as $key => $value){
            if (is_string($value))
            {
                if (isset($this->formDefinition['adapter']) && array_key_exists($value,$this->formDefinition['adapter'])){
                    $tempAdapter = $this->formDefinition['adapter'][$value];
                    unset($formConfig['adapter'][$key]);
                    $formConfig['adapter'] = array_merge($formConfig['adapter'],$tempAdapter);
                }
            }
        }
        return $formConfig;
    }

    /**
     * Wandelt Request-Daten in PHP-Array um.
     *
     * @param JSON
     * @return Array
     */
    private function decodeData($jsonData){

        try {
            $data = Json\Json::decode($jsonData, Json\Json::TYPE_ARRAY);
        } catch (Exception $e) {
            return $this->respond(
                "Error decoding request. Expecting valid JSON!",
                400,
                self::STATUS_INVALID_REQUEST
            );
        }
        if (!array_key_exists('action', $data) || !array_key_exists('data', $data)) {
            return $this->respond(
                "Error decoding request. Invalid format!",
                400,
                self::STATUS_INVALID_REQUEST
            );
        }
        return $data;
    }
}
