<?php

namespace ElectricBrands\FormularHandlerMiddleware\Adapter;

/**
 * Stellt die replyTo Funktion bereit.
 *
 * Trait ReplyToTrait
 */
trait ReplyToTrait
{
    /**
     * Gibt, sofern konfiguriert eine Email zurück, die als reply-to verwendet wird.
     *
     * @param $mailData array
     *
     * @return string|null
     */
    public function replyTo($mailData)
    {
        $replyTo = null;
        if (!isset($mailData['reply-to'])) {
            return $replyTo;
        }
        if (!is_array($mailData['reply-to'])) {
            $this->setError('reply-to must be an array in config!');

            return;
        }
        if (!isset($mailData['reply-to']['status'])) {
            $this->setError('reply-to status not found!');

            return $replyTo;
        }

        if (!$mailData['reply-to']['status']) {
            return $replyTo;
        }

        if (!isset($mailData['reply-to']['field'])) {
            foreach ($this->config['fields'] as $key => $field) {
                if (isset($field['type']) && $field['type'] == 'email') {
                    $replyTo = $this->validFields[$key];
                    break;
                }
            }
        } else {
            if (!isset($this->validFields[$mailData['reply-to']['field']]) || is_array($this->validFields[$mailData['reply-to']['field']]) || is_null($this->validFields[$mailData['reply-to']['field']])) {
                $this->setError('email-field for reply-to not found');

                return;
            }
            $replyTo = $this->validFields[$mailData['reply-to']['field']];
        }
        if (is_null($replyTo)) {
            $this->setError('reply-to was enable but couldn\'t find a field with type email.');

            return;
        }

        return $replyTo;
    }
}
