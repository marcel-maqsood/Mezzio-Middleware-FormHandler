<?php

namespace depa\FormularHandlerMiddleware\Adapter;

use depa\FormularHandlerMiddleware\AbstractAdapter;
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
    protected function checkConfig($config)
    {
        if (!isset($config['adapter']) || is_null($config['adapter']) || !is_array($config['adapter'])) {
            parent::setError('The adapter was not found in config!');

            return;
        }
        if (!isset($config['adapter']['smtpmail']) || is_null($config['adapter']['smtpmail']) || !is_array($config['adapter']['smtpmail'])) {
            parent::setError('There is no mail-config inside the adapter!');

            return;
        }
        $mailConfig = $config['adapter']['smtpmail'];

        if (!isset($mailConfig['email_transfer']) || is_null($mailConfig['email_transfer']) || !is_array($mailConfig['email_transfer'])) {
            //Fehler: keine gültige config für das versenden von mails
            parent::setError('There is no email_transfer defined inside the mail-adapter config!');

            return;
        }
        $mailTransfer = $mailConfig['email_transfer'];

        if (!isset($mailTransfer['method']) || is_null($mailTransfer['method']) || is_array($mailTransfer['method'])) {
            //Fehler: keine gültige definition für Transfer-Methode
            parent::setError('There is no method defined inside the email_transfer config!');

            return;
        }

        if (!isset($mailTransfer['config']) || is_null($mailTransfer['config']) || !is_array($mailTransfer['config'])) {
            //Fehler: ungültige Config für Transfer-Methode
            parent::setError('There is no mail-config defined inside the email_transfer config!');

            return;
        }

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

    /**
     * Rendert und versendet die EMail.
     */
    public function handleData()
    {
        $formData = $this->validFields;
        $mailData = $this->config['adapter']['smtpmail']; //macht sinn das in abstractadapter auszulagern, und den config eintrag per klassen variable zu setzen (['adapter'][$this->name];)?

        try {
            $loader = new Twig\Loader\ArrayLoader([
                'mailMessage.html' => $mailData['template'],
            ]);
            $twig = new Twig\Environment($loader);

            $mailMessage = $twig->render('mailMessage.html', $formData);

            $replyTo = $this->replyTo($mailData);
            if (!$this->errorStatus) {
                $this->sendMail($mailData, $mailMessage, $replyTo);
            }
        } catch (Exception $e) {
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
        foreach ($this->validFields as $key => $value) {
            $replacements[$key] = $value;
        }

        $mailSubject = $twig->render('mailSubject.html', $replacements);
        $mailer = new Swift_Mailer($transport);

        foreach ($mailData['recipients'] as $recipient) {
            $message = (new Swift_Message())
                ->setSubject($mailSubject)
                ->setFrom([$mailData['sender'] => $mailData['senderName']])
                ->setTo([$recipient])
                ->setBody($mailMessage);
            if (!is_null($replyTo)) {
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
        $transfer_config = $this->config['adapter']['smtpmail']['email_transfer']['config'];

        if (!isset($transfer_config['service']) || is_null($transfer_config['service'])) {
            parent::setError('SMTP transfer-method requires a service to use!');
        }
        $service = $transfer_config['service'];

        if (!isset($transfer_config['port']) || is_null($transfer_config['port'])) {
            parent::setError('SMTP transfer-method requires a port to use!');
        }
        $port = $transfer_config['port'];

        if ($service == 'localhost') {
            return new Swift_SmtpTransport($service, $port);
        }

        if (!isset($transfer_config['encryption']) || is_null($transfer_config['encryption'])) {
            parent::setError('SMTP transfer-method requires a encryption method!');
        }
        $encryption = $transfer_config['encryption'];

        if (!isset($transfer_config['email']) || is_null($transfer_config['email'])) {
            parent::setError('SMTP transfer-method requires a email!');
        }
        $email = $transfer_config['email'];

        if (!isset($transfer_config['password']) || is_null($transfer_config['password'])) {
            parent::setError('SMTP transfer-method requires the password of '.$email.'!');
        }
        $password = $transfer_config['password'];

        return (new Swift_SmtpTransport($service, $port, $encryption))
            ->setUsername($email)
            ->setPassword($password);
    }
}
