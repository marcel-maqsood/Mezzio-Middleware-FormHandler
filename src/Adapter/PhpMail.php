<?php


namespace depa\FormularHandlerMiddleware\Adapter;

use depa\FormularHandlerMiddleware\AbstractAdapter;

class PhpMail extends AbstractAdapter
{

    /**
     * @param $config
     * @return array
     */
    protected function checkConfig($config)
    {
        if (!isset($config['adapter']) || is_null($config['adapter']) || !is_array($config['adapter'])) {
            parent::setError('The adapter was not found in config!');
            return null;
        }
        if (!isset($config['adapter']['phpmail']) || is_null($config['adapter']['phpmail']) || !is_array($config['adapter']['phpmail'])) {
            parent::setError('There is no mail-config idnside the adapter!');
            return null;
        }
        $mailConfig = $config['adapter']['phpmail'];

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
     * Versendet eine Email an die in der Array-Config hinterlegten recipient(s), basierend auf dem dort definierten Template.
     */
    public function handleData()
    {
        $formData = $this->validFields;
        $mailData = $this->config['adapter']['phpmail'];

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

        if(!is_null($this->subject)){
            $mailData['subject'] = str_replace('{subject}', $this->subject, $mailData['subject']);
        }
        foreach ($mailData['recipients'] as $recipient) {
            $header = array(
                'From' => $mailData['sender']
            );
            mail(
                $recipient,
                $mailData['subject'],
                $mailMessage,
                $header
            );
        }
    }
}