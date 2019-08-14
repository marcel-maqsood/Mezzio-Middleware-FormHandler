<?php


namespace depa\FormularHandlerMiddleware\Adapter;


use depa\FormularHandlerMiddleware\AbstractAdapter;
use Psr\Http\Message\ResponseInterface;

class Mail extends AbstractAdapter
{

    protected function checkConfig($config)
    {
            $mailConfig = $config['adapter']['mail'];
        if (!isset($mailConfig) || is_null($mailConfig) ||!is_array($mailConfig)){
            //Hier ebenfalls zendproblem-details...
        }

        $mailTransfer = $mailConfig['email_transfer'];
        if(!isset($mailTransfer) || is_null($mailTransfer) || !is_array($mailTransfer)){
            //Fehler: keine gültige config für das versenden von mails
        }

        $mailTransferMethod = $mailTransfer['method'];
        if(!isset($mailTransferMethod) || is_null($mailTransferMethod) || is_array($mailTransferMethod)){
            //Fehler: keine gültige definition für Transfer-Methode
        }

        $mailTransferConfig = $mailTransfer['config'];
        if(!isset($mailTransferConfig) || is_null($mailTransferConfig) || !is_array($mailTransferConfig)){
            //Fehler: ungültige Config für Transfer-Methode
        }

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