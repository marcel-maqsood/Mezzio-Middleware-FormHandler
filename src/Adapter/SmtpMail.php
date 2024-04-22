<?php

namespace MazeDEV\FormularHandlerMiddleware\Adapter;

use MazeDEV\FormularHandlerMiddleware\AbstractAdapter;
use Exception;
use Swift_Mailer;
use Swift_Message;
use Swift_SmtpTransport;
use Twig;

/**
 * Sendet Mail über Swift_SmtpTransport.
 *
 * Class SmtpMail
 */
class SmtpMail extends AbstractAdapter
{
    use ReplyToTrait;

    /**
     * Überprüft das übergebene Config-Array.
     *
     * @param $config
     *
     * @return array |null
     */
    protected function checkConfig($adapter)
    {
        if (!isset($adapter['email_transfer']) || is_null($adapter['email_transfer']) || !is_array($adapter['email_transfer'])) 
        {
            //Fehler: keine gültige config für das versenden von mails
            parent::setError('There is no email_transfer defined inside the mail-adapter config!');

            return;
        }
        $mailTransfer = $adapter['email_transfer'];

        if (!isset($mailTransfer['config']) || is_null($mailTransfer['config']) || !is_array($mailTransfer['config'])) 
        {
            //Fehler: ungültige Config für Transfer-Methode
            parent::setError('There is no mail-config defined inside the email_transfer config!');

            return;
        }

        if (!isset($adapter['recipients']) || is_null($adapter['recipients']) || !is_array($adapter['recipients'])) 
        {
            //Fehler: ungültige definition von recipients
            parent::setError('recipients-arraay is not properly defined in adapter!');

            return;
        }

        if (!isset($adapter['subject']) || is_null($adapter['subject']) || !is_string($adapter['subject'])) 
        {
            //Fehler: ungültige definition von Subject
            parent::setError('There is no subject defined inside the adapter!');

            return;
        }

        if (!isset($adapter['sender']) || is_null($adapter['sender']) || !is_string($adapter['sender'])) 
        {
            //Fehler: ungültige definition von Subject
            parent::setError('There is no sender defined inside the adapter!');

            return;
        }

        if (!isset($adapter['senderName']) || is_null($adapter['senderName']) || !is_string($adapter['senderName'])) 
        {
            //Fehler: ungültige definition von Subject
            parent::setError('There is no senderName defined inside the adapter!');

            return;
        }

        return $adapter;
    }

    /**
     * Rendert und versendet die EMail.
     */
    public function handleData()
    {
        try 
        {

            $mailMessage = $this->renderer->render($this->adapter['template'], $this->validFields);

            $replyTo = $this->replyTo($this->adapter);
            if (!$this->errorStatus) 
            {
                $this->sendMail($this->adapter, $mailMessage, $replyTo);
            }
        } 
        catch (Exception $e) 
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
        $transport = $this->createTransporter();

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
        $mailer = new Swift_Mailer($transport);

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

            $message = (new Swift_Message())
                ->setSubject($mailSubject)
                ->setFrom([$mailData['sender'] => $mailData['senderName']])
                ->setTo([$recipient])
                ->setBody($mailMessage, 'text/html');
            if (!is_null($replyTo)) 
            {
                $message = $message->setReplyTo($replyTo);
            }
            $mailer->send($message);
        }
    }

    /**
     * Erstellt ein Swift_SmtpTransport Objekt, über das Mails versendet werden.
     *
     * @return Swift_SmtpTransport
     */
    private function createTransporter()
    {
        $transfer_config = $this->adapter['email_transfer']['config'];

        if (!isset($transfer_config['service']) || is_null($transfer_config['service'])) 
        {
            parent::setError('SMTP transfer-method requires a service to use!');
        }
        $service = $transfer_config['service'];

        if (!isset($transfer_config['port']) || is_null($transfer_config['port'])) 
        {
            parent::setError('SMTP transfer-method requires a port to use!');
        }
        $port = $transfer_config['port'];

        if ($service == 'localhost') 
        {
            return new Swift_SmtpTransport($service, $port);
        }

        if (!isset($transfer_config['encryption']) || is_null($transfer_config['encryption'])) 
        {
            parent::setError('SMTP transfer-method requires a encryption method!');
        }
        $encryption = $transfer_config['encryption'];

        if (!isset($transfer_config['email']) || is_null($transfer_config['email'])) 
        {
            parent::setError('SMTP transfer-method requires a email!');
        }
        $email = $transfer_config['email'];

        if (!isset($transfer_config['password']) || is_null($transfer_config['password'])) 
        {
            parent::setError('SMTP transfer-method requires the password of '.$email.'!');
        }
        $password = $transfer_config['password'];

        return (new Swift_SmtpTransport($service, $port, $encryption))
            ->setUsername($email)
            ->setPassword($password);
    }
}
