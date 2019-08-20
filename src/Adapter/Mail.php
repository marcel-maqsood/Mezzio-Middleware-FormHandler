<?php


namespace depa\FormularHandlerMiddleware\Adapter;


use depa\FormularHandlerMiddleware\AbstractAdapter;
use Psr\Http\Message\ResponseInterface;

class Mail extends AbstractAdapter
{

    private $transport;

    /**
     * Prüft die übergebene Config (beinhaltet den Adapter) nach den benötigten Werten.
     * @param $config
     * @return |null
     */
    protected function checkConfig($config)
    {
        if(!isset($config['adapter']) || is_null($config['adapter']) || !is_array($config['adapter'])){
            parent::setError('The adapter was not found in config!');
            return null;
        }
        if (!isset($config['adapter']['mail']) || is_null($config['adapter']['mail']) ||!is_array($config['adapter']['mail'])){
            parent::setError('There is no mail-config inside the adapter!');
            return null;
        }
        $mailConfig = $config['adapter']['mail'];

        if(!isset($mailConfig['email_transfer']) || is_null($mailConfig['email_transfer']) || !is_array($mailConfig['email_transfer'])){
            //Fehler: keine gültige config für das versenden von mails
            parent::setError('There is no email_transfer defined inside the mail-adapter config!');
            return null;
        }
        $mailTransfer = $mailConfig['email_transfer'];

        if(!isset($mailTransfer['method']) || is_null($mailTransfer['method']) || is_array($mailTransfer['method'])){
            //Fehler: keine gültige definition für Transfer-Methode
            parent::setError('There is no method defined inside the email_transfer config!');
            return null;
        }

        if(!isset($mailTransfer['config']) || is_null($mailTransfer['config']) || !is_array($mailTransfer['config'])){
            //Fehler: ungültige Config für Transfer-Methode
            parent::setError('There is no mail-config defined inside the email_transfer config!');
            return null;
        }

        if(!isset($mailConfig['recipients']) || is_null($mailConfig['recipients']) || !is_array($mailConfig['recipients'])){
            //Fehler: ungültige definition von recipients
            parent::setError('recipients-arraay is not properly defined in email_transfer-config!');
            return null;
        }

        if(!isset($mailConfig['subject']) || is_null($mailConfig['subject']) || !is_string($mailConfig['subject'])){
            //Fehler: ungültige definition von Subject
            parent::setError('Thereis no subject defined inside the transfer_config!');
            return null;
        }

        if(!isset($mailConfig['sender']) || is_null($mailConfig['sender']) || !is_string($mailConfig['sender'])){
            //Fehler: ungültige definition von Subject
            parent::setError('Thereis no sender defined inside the transfer_config!');
            return null;
        }

        if(!isset($mailConfig['senderName']) || is_null($mailConfig['senderName']) || !is_string($mailConfig['senderName'])){
            //Fehler: ungültige definition von Subject
            parent::setError('Thereis no senderName defined inside the transfer_config!');
            return null;
        }

        return $config;
    }

    /**
     * Versendet eine Email an die in der Array-Config hinterlegten recipient(s), basierend auf dem dort definierten Template.
     * @return ResponseInterface
     */
    public function handleData(
        
    ) : ResponseInterface
    {
        $formData = $this->validFields;
        $mailData = $this->config['adapter']['mail'];

        //Verwenden eines try-catch blocks, um auch bei fehlern mit problem-details zu arbeiten.
        try{
            $this->transport = $this->currentTransferer();
            //Message in Config-Array des Formulars aufbauen - dabei die field-names mit {} umklammern, um es für das System erkennbar zu machen?
            //$mailMessage = $this->renderTemplate($formData, $mailData['template'], $mailData);
            $loader = new \Twig\Loader\ArrayLoader([
                'test.html' => $mailData['template'],
            ]);
            $twig = new \Twig\Environment($loader);

            $mailMessage = $twig->render('test.html', $formData);
            foreach ($mailData['recipients'] as $recipient){
                $mailer = new \Swift_Mailer($this->transport);
                $message = (new \Swift_Message())
                    ->setSubject($mailData['subject'])
                    ->setFrom([$mailData['sender'] => $mailData['senderName']])
                    ->setTo([$recipient])
                    ->setBody($mailMessage);
                $mailer->send($message);

            }
            //hier nichts zurückgeben, damit das program (später) weiß, dass hier alles gut ging und ein 200er gegeben werden kann.
        }catch(\Exception $e){
            parent::setError('Error occourd in: ' . $e->getFile() . ' on line: ' . $e->getLine() . ' with message: ' . $e->getMessage());
            return new HtmlResponse('Error');
        }
        return new HtmlResponse('Ok');
    }

    /**
     * @return \Swift_SmtpTransport|null
     */
    private function currentTransferer()
    {
        $transfer_config = $this->config['adapter']['mail']['email_transfer'];

        if (!isset($transfer_config['method'])
            || is_null($transfer_config['method'])) {
            parent::setError("transfer-method must be set!");
        }

        $transport = null;
        $method = $transfer_config['method'];
        $transfer_config = $transfer_config['config'];
        switch ($method) {
            case "smtp":
                if (!isset($transfer_config['service']) || is_null($transfer_config['service'])) {
                    parent::setError("SMTP transfer-method requires a service to use!");
                }
                $service = $transfer_config['service'];

                if (!isset($transfer_config['port']) || is_null($transfer_config['port'])) {
                    parent::setError("SMTP transfer-method requires a port to use!");

                }
                $port = $transfer_config['port'];

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

                $transport = (new \Swift_SmtpTransport($service, $port, $encryption))
                    ->setUsername($email)
                    ->setPassword($password);

                break;
            //Hier können weitere Funktioen angefügt werden,
        }
        return $transport;
    }
}