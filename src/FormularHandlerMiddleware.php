<?php

declare(strict_types=1);

namespace depa\FormularHandlerMiddleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Zend\Diactoros\Response\JsonResponse;


class FormularHandlerMiddleware implements RequestHandlerInterface
{
    /**
     * @var TemplateRendererInterface
     */
    private $renderer;

    private $formDefinition;

    private $transport;

    public function __construct(TemplateRendererInterface $renderer, $formDefinition)
    {
        $this->renderer = $renderer;
        $this->formDefinition = $formDefinition;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $formUtils =  new \Formular\Utils\Utils($this->formDefinition);
        $submittedData = $formUtils->decodeData($request->getBody()->getContents());
        $submittedData = $submittedData['data'];
        if (!isset($submittedData['config'])) {
            return $formUtils->respond(
                "submitted formular does not contains a config declaration!",
                400,
                self::STATUS_MISSING_VALUE
            );
        }
        $configName = $submittedData['config'];

        $email_informations = $formUtils->getEmailData($configName);

        $transfer_config = $email_informations['transfer_config'];
        $formDefinition = $email_informations['formDefinition'];
        $recipients = $email_informations['recipients'];
        $subject = $email_informations['subject'];
        $sender = $email_informations['sender'];
        $senderName = $email_informations['senderName'];
        $template = $email_informations['template'];

        $templateVariables = ['subject' => $subject];

        foreach ($formDefinition['fields'] as $fieldName => $fieldEntry) {
            if (isset($fieldEntry['required']) && $fieldEntry['required'] == true) {
                if (!isset($submittedData[$fieldName])) {
                    return $formUtils->respond(
                        "Formular is not valid since "
                        . $fieldName . " is not defined in submitted data!",
                        400,
                        self::STATUS_CONFIG_ERROR
                    );
                }
            }

            if (isset($submittedData[$fieldName])) {
                $templateVariables[$fieldName] = $submittedData[$fieldName];
            }
        }

        $this->transport = $formUtils->getTransferer($transfer_config);
        $renderedMessage = $this->renderer->render(
            $template,
            $templateVariables
        );
        foreach ($recipients as $recipient) {
            $mailer = new \Swift_Mailer($this->transport);

            $message = (new \Swift_Message())
                ->setSubject($subject)
                ->setFrom([$sender => $senderName])
                ->setTo([$recipient])
                ->setBody($renderedMessage);
            $result = $mailer->send($message);
        }

        return $formUtils->respond(
            "E-mail was successful send!",
            200,
            $formUtils::STATUS_OK
        );
    }
}
