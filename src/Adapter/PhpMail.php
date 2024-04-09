<?php

namespace MazeDEV\FormularHandlerMiddleware\Adapter;

use MazeDEV\FormularHandlerMiddleware\AbstractAdapter;
use Exception;
use Twig;

/**
 * Versendet Mail über PHP mail().
 *
 * Class PhpMail
 */
class PhpMail extends AbstractAdapter
{
    use ReplyToTrait;

    /**
     * Rendert und versendet die EMail.
     */
    public function handleData()
    {
        $formData = $this->validFields;
        $mailData = $this->config['adapter']['phpmail'];

        try 
        {
            $loader = new Twig\Loader\ArrayLoader([
                'mailMessage.html' => $mailData['template'],
            ]);
            $twig = new Twig\Environment($loader);

            $mailMessage = $twig->render('mailMessage.html', $formData);

            $replyTo = $this->replyTo($mailData);
            if (!$this->errorStatus) 
            {
                $this->sendMail($mailData, $mailMessage, $replyTo);
            }
        } catch (Exception $e) 
        {
            parent::setError('Error occourd in: '.$e->getFile().' on line: '.$e->getLine().' with message: '.$e->getMessage());
        }
    }

    /**
     * Sendet eine EMail.
     *
     * @param $mailData
     * @param $mailMessage
     * @param $replyTo
     *
     * @throws Twig\Error\LoaderError
     * @throws Twig\Error\RuntimeError
     * @throws Twig\Error\SyntaxError
     */
    private function sendMail($mailData, $mailMessage, $replyTo)
    {
        $loader = new Twig\Loader\ArrayLoader([
            'mailSubject.html' => $mailData['subject'],
        ]);
        $twig = new Twig\Environment($loader);

        $replacements = [];
        foreach ($this->validFields as $key => $value) 
        {
            $replacements[$key] = $value;
        }

        $mailSubject = $twig->render('mailSubject.html', $replacements);

        $header = [
            'From' => $mailData['sender'],
        ];
        if (!is_null($replyTo)) {
            $header['Reply-to'] = $replyTo;
        }

        foreach ($mailData['recipients'] as $recipient) 
        {
            mail(
                $recipient,
                $mailSubject,
                $mailMessage,
                $header
            );
        }
    }

    /**
     * Überprüft das übergebene Config-Array.
     *
     * @param $config
     *
     * @return array |null
     */
    protected function checkConfig($config)
    {
        if (!isset($config['adapter']) || is_null($config['adapter']) || !is_array($config['adapter'])) {
            parent::setError('The adapter was not found in config!');

            return;
        }
        if (!isset($config['adapter']['phpmail']) || is_null($config['adapter']['phpmail']) || !is_array($config['adapter']['phpmail'])) {
            parent::setError('There is no mail-config inside the adapter!');

            return;
        }
        $mailConfig = $config['adapter']['phpmail'];

        if (!isset($mailConfig['recipients']) || is_null($mailConfig['recipients']) || !is_array($mailConfig['recipients'])) {
            //Fehler: ungültige definition von recipients
            parent::setError('recipients-arraay is not properly defined in adapter!');

            return;
        }

        if (!isset($mailConfig['subject']) || is_null($mailConfig['subject']) || !is_string($mailConfig['subject'])) {
            //Fehler: ungültige definition von Subject
            parent::setError('There is no subject defined inside the adapter!');

            return;
        }

        if (!isset($mailConfig['sender']) || is_null($mailConfig['sender']) || !is_string($mailConfig['sender'])) {
            //Fehler: ungültige definition von Subject
            parent::setError('There is no sender defined inside the adapter!');

            return;
        }

        if (!isset($mailConfig['senderName']) || is_null($mailConfig['senderName']) || !is_string($mailConfig['senderName'])) {
            //Fehler: ungültige definition von Subject
            parent::setError('There is no senderName defined inside the adapter!');

            return;
        }

        return $config;
    }
}
