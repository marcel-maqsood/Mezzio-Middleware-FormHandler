<?php


namespace depa\FormularHandlerMiddleware\Adapter;


use depa\FormularHandlerMiddleware\AbstractAdapter;
use Psr\Http\Message\ResponseInterface;

class Wufoo extends AbstractAdapter
{

    /**
     * @param $config
     */
    protected function checkConfig($config)
    {
        // TODO: Implement checkConfig() method.
    }

    /**
     * @return ResponseInterface
     */
    public function handleData() : ResponseInterface
    {
        // TODO: Implement handleData() method.
        return null;
    }
}