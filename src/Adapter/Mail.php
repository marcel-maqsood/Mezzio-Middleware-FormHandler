<?php


namespace depa\FormularHandlerMiddleware\Adapter;


use depa\FormularHandlerMiddleware\AbstractAdapter;
use Psr\Http\Message\ResponseInterface;

class Mail extends AbstractAdapter
{

    protected function checkConfig($config)
    {



        if (!isset($config['adapter']['mail']) || is_null($config['adapter']['mail']) ||!is_array($config['adapter']['mail'])){
            //Hier ebenfalls zendproblem-details...
        }
        $mailConfig = $config['adapter']['mail'];

        if(!isset($mailConfig['email_transfer']) || is_null($mailConfig['email_transfer']) || !is_array($mailConfig['email_transfer'])){
            //Fehler: keine gültige config für das versenden von mails
        }
        $mailTransfer = $mailConfig['email_transfer'];

        if(!isset($mailTransfer['method']) || is_null($mailTransfer['method']) || is_array($mailTransfer['method'])){
            //Fehler: keine gültige definition für Transfer-Methode
        }
        $mailTransferMethod = $mailTransfer['method'];

        if(!isset($mailTransfer['config']) || is_null($mailTransfer['config']) || !is_array($mailTransfer['config'])){
            //Fehler: ungültige Config für Transfer-Methode
        }
        $mailTransferConfig = $mailTransfer['config'];

        if(!isset($mailConfig['recipients']) || is_null($mailConfig['recipients']) || !is_array($mailConfig['recipients'])){
            //Fehler: ungültige definition von recipients
        }
        $mailRecipients = $mailConfig['recipients'];

        if(!isset($mailConfig['subject']) || is_null($mailConfig['subject']) || !is_string($mailConfig['subject'])){
            //Fehler: ungültige definition von Subject
        }
        $mailSubject = $mailConfig['subject'];

        if(!isset($mailConfig['sender']) || is_null($mailConfig['sender']) || !is_string($mailConfig['sender'])){
            //Fehler: ungültige definition von Subject
        }
        $mailSender = $mailConfig['sender'];

        if(!isset($mailConfig['senderName']) || is_null($mailConfig['senderName']) || !is_string($mailConfig['senderName'])){
            //Fehler: ungültige definition von Subject
        }
        $mailSenderName = $mailConfig['senderName'];
    }

    public function handleData() : ResponseInterface
    {

        //Verwenden eines try-catch blocks, um auch bei fehlern mit problem-details zu arbeiten.
        try{

            //die Daten, die hier drin sind, sind die Inhalte der Felder (mit Bezeichner), die mit der Config übereinstanden.
            $mailData =  $this->templateVariables;
            $mailMessage = "Anfrage über Formular von " . $mailData['name'] . " eingegangen!\n\nFolgende Nachricht wurde angehängt: " . $mailData['message']; //Message aufbau im Array des jeweiligen Formulars definieren?
            //TODO: Hier muss eine mail-versende Funktion geschaffen werden, die mit dem Daten-Array arbeitet.
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

            //problem-details->createResponseFromThrowable muss hier angewand werden

        }

    }


}