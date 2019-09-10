<?php

namespace depa\FormularHandlerMiddleware;

trait MailTrait{

    /**
     * @param $mailData
     * @return |null
     */
    public function replyTo($mailData){

        $replyTo = null;
        if(!isset($mailData['reply-to']) || !is_array($mailData['reply-to'])){
            return $replyTo;
        }
        if(!isset($mailData['reply-to']['status'])){
            $this->setError('reply-to status not found!');
            return $replyTo;
        }

        if(!$mailData['reply-to']['status']){
            return $replyTo;
        }

        if(!isset($mailData['reply-to']['field'])){
            foreach ($this->config['fields'] as $key => $field){
                if(isset($field['type']) && $field['type']  == 'email'){
                    $replyTo = $this->validFields[$key];
                    break;
                }
            }
        }else{
            if(!isset($this->validFields[$mailData['reply-to']['field']]) || is_array($this->validFields[$mailData['reply-to']['field']]) || is_null($this->validFields[$mailData['reply-to']['field']])){
                $this->setError('reply-to field not found');
                return null;
            }
            $replyTo = $this->validFields[$mailData['reply-to']['field']];
        }
        if(is_null($replyTo)){
            $this->setError('no reply-to email found.');
            return null;
        }
        return $replyTo;
    }
}