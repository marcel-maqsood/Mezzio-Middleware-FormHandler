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

        try 
        {
            $loader = new Twig\Loader\ArrayLoader([
                'mailMessage.html' => $this->adapter['template'],
            ]);
            $twig = new Twig\Environment($loader);

            $mailMessage = $twig->render('mailMessage.html', $formData);

            $replyTo = $this->replyTo($this->adapter);
            if (!$this->errorStatus) 
            {
                $this->sendMail($this->adapter, $mailMessage, $replyTo);
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

            //if we encounter a recipient that does not contain an '@', we handle it like a variable and replace it if possible; otherwise it will be discarded.
            if(!strpos($recipient, "@"))
            {
                if($recipient != '%submit%')
                {
                    //We could add more error handling; 
                    //but for now we just ignore the recipient if he doesnt have a valid email and is not %submit%.
                    continue;
                }
 
                if($this->submitEmail == null)
                {
                    //if the recipient is %submit% and we dont have a submitEmail (maybe casue the form didn't had one set)
                    //then the handler cant map it so we ignore it.
                    continue;
                }
                $recipient = $this->submitEmail;
            }

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
        if (!isset($config['recipients']) || is_null($config['recipients']) || !is_array($config['recipients'])) 
        {
            //Fehler: ungültige definition von recipients
            parent::setError('recipients-arraay is not properly defined in adapter!');

            return;
        }

        if (!isset($config['subject']) || is_null($config['subject']) || !is_string($config['subject'])) 
        {
            //Fehler: ungültige definition von Subject
            parent::setError('There is no subject defined inside the adapter!');

            return;
        }

        if (!isset($config['sender']) || is_null($config['sender']) || !is_string($config['sender'])) 
        {
            //Fehler: ungültige definition von Subject
            parent::setError('There is no sender defined inside the adapter!');

            return;
        }

        if (!isset($config['senderName']) || is_null($config['senderName']) || !is_string($config['senderName'])) 
        {
            //Fehler: ungültige definition von Subject
            parent::setError('There is no senderName defined inside the adapter!');

            return;
        }

        return $config;
    }
}
