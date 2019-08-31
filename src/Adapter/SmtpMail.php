<?php


namespace depa\FormularHandlerMiddleware\Adapter;

use depa\FormularHandlerMiddleware\AbstractAdapter;

class SmtpMail extends AbstractAdapter
{

    /**
     * Prüft die übergebene Config (beinhaltet den Adapter) nach den benötigten Werten.
     * @param $config
     * @return |null
     */
    protected function checkConfig($config)
    {
        if (!isset($config['adapter']) || is_null($config['adapter']) || !is_array($config['adapter'])) {
            parent::setError('The adapter was not found in config!');
            return null;
        }
        if (!isset($config['adapter']['smtpmail']) || is_null($config['adapter']['smtpmail']) || !is_array($config['adapter']['smtpmail'])) {
            parent::setError('There is no mail-config inside the adapter!');
            return null;
        }
        $mailConfig = $config['adapter']['smtpmail'];

        if (!isset($mailConfig['email_transfer']) || is_null($mailConfig['email_transfer']) || !is_array($mailConfig['email_transfer'])) {
            //Fehler: keine gültige config für das versenden von mails
            parent::setError('There is no email_transfer defined inside the mail-adapter config!');
            return null;
        }
        $mailTransfer = $mailConfig['email_transfer'];

        if (!isset($mailTransfer['method']) || is_null($mailTransfer['method']) || is_array($mailTransfer['method'])) {
            //Fehler: keine gültige definition für Transfer-Methode
            parent::setError('There is no method defined inside the email_transfer config!');
            return null;
        }

        if (!isset($mailTransfer['config']) || is_null($mailTransfer['config']) || !is_array($mailTransfer['config'])) {
            //Fehler: ungültige Config für Transfer-Methode
            parent::setError('There is no mail-config defined inside the email_transfer config!');
            return null;
        }

        if (!isset($mailConfig['recipients']) || is_null($mailConfig['recipients']) || !is_array($mailConfig['recipients'])) {
            //Fehler: ungültige definition von recipients
            parent::setError('recipients-arraay is not properly defined in email_transfer-config!');
            return null;
        }

        if (!isset($mailConfig['subject']) || is_null($mailConfig['subject']) || !is_string($mailConfig['subject'])) {
            //Fehler: ungültige definition von Subject
            parent::setError('There is no subject defined inside the transfer_config!');
            return null;
        }

        if (!isset($mailConfig['sender']) || is_null($mailConfig['sender']) || !is_string($mailConfig['sender'])) {
            //Fehler: ungültige definition von Subject
            parent::setError('There is no sender defined inside the transfer_config!');
            return null;
        }

        if (!isset($mailConfig['senderName']) || is_null($mailConfig['senderName']) || !is_string($mailConfig['senderName'])) {
            //Fehler: ungültige definition von Subject
            parent::setError('There is no senderName defined inside the transfer_config!');
            return null;
        }

        return $config;
    }

    /**
     * @return mixed|void
     */
    public function handleData()
    {
        $formData = $this->validFields;
        $mailData = $this->config['adapter']['smtpmail'];

        //Verwenden eines try-catch blocks, um auch bei fehlern mit problem-details zu arbeiten.
        try {
            //Message in Config-Array des Formulars aufbauen - dabei die field-names mit {} umklammern, um es für das System erkennbar zu machen?
            //$mailMessage = $this->renderTemplate($formData, $mailData['template'], $mailData);
            $loader = new \Twig\Loader\ArrayLoader([
                'test.html' => $mailData['template'],
            ]);
            $twig = new \Twig\Environment($loader);

            $mailMessage = $twig->render('test.html', $formData);

            $this->sendMail($mailData, $mailMessage);
            //hier nichts zurückgeben, damit das program (später) weiß, dass hier alles gut ging und ein 200er gegeben werden kann.
        } catch (\Exception $e) {
            parent::setError('Error occourd in: ' . $e->getFile() . ' on line: ' . $e->getLine() . ' with message: ' . $e->getMessage());
        }
    }

    /**
     * @param $mailData
     * @param $mailMessage
     */
    private function sendMail($mailData, $mailMessage)
    {
        $transport = $this->createTransporter();

        foreach ($mailData['recipients'] as $recipient) {
            $mailer = new \Swift_Mailer($transport);
            $message = (new \Swift_Message())
                ->setSubject($mailData['subject'])
                ->setFrom([$mailData['sender'] => $mailData['senderName']])
                ->setTo([$recipient])
                ->setBody($mailMessage);
            $mailer->send($message);
        }
    }

    private function createTransporter(){

        $transfer_config = $this->config['adapter']['smtpmail']['email_transfer']['config'];

        if (!isset($transfer_config['service']) || is_null($transfer_config['service'])) {
            parent::setError("SMTP transfer-method requires a service to use!");
        }
        $service = $transfer_config['service'];

        if (!isset($transfer_config['port']) || is_null($transfer_config['port'])) {
            parent::setError("SMTP transfer-method requires a port to use!");

        }
        $port = $transfer_config['port'];

        if($service == "localhost"){
            return new \Swift_SmtpTransport($service, $port);
        }

        if (!isset($transfer_config['encryption']) || is_null($transfer_config['encryption'])) {
            parent::setError("SMTP transfer-method requires a encryption method!");
        }
        $encryption = $transfer_config['encryption'];

        if (!isset($transfer_config['email']) || is_null($transfer_config['email'])) {
            parent::setError("SMTP transfer-method requires a email!");
        }
        $email = $transfer_config['email'];

        if (!isset($transfer_config['password']) || is_null($transfer_config['password'])) {
            parent::setError("SMTP transfer-method requires the password of " . $email . "!");
        }
        $password = $transfer_config['password'];

        return (new \Swift_SmtpTransport($service, $port, $encryption))
            ->setUsername($email)
            ->setPassword($password);

    }
}