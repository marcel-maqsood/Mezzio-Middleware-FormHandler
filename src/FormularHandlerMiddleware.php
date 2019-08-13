<?php

declare(strict_types=1);

namespace depa\FormularHandlerMiddleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Zend\Diactoros\Response\JsonResponse;
use Zend\Json\Json;


class FormularHandlerMiddleware implements RequestHandlerInterface
{

    const STATUS_MISSING_VALUE = 'MISSING_VALUE';

    /**
     * @var TemplateRendererInterface
     */
    private $renderer;

    private $formDefinition;

    private $transport;

    private $problem_details;

    public function __construct(TemplateRendererInterface $renderer, $formDefinition, $problem_details)
    {
        $this->renderer = $renderer;
        $this->formDefinition = $formDefinition;
        $this->problem_details = $problem_details;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $data = Json::decode($request->getBody()->getContents(), Json::TYPE_ARRAY);
        //evtl. überprüfen?
        if(is_null($data) || !is_array($data)){
            //Api problem shoot
            return $this->problem_details->createResponse(
                $this->request,
                400,
                "Data was missing in submitted form.",
                self::STATUS_MISSING_VALUE,
                "N/A"
            );
        }

        $data = $data['data'];



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
