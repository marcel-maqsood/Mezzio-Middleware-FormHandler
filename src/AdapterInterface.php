<?php
declare(strict_types=1);

namespace depa\FormularHandlerMiddleware;


interface AdapterInterface
{
    public function handleData();
}