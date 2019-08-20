<?php


namespace depa\FormularHandlerMiddleware\Adapter;


use depa\FormularHandlerMiddleware\AbstractAdapter;
use Psr\Http\Message\ResponseInterface;

class PdoDatabase extends AbstractAdapter
{

    protected function checkConfig($config)
    {
        // TODO: Implement checkConfig() method.
    }

    public function handleData() : ResponseInterface
    {
        // TODO: Implement handleData() method.
        return null;
    }
}