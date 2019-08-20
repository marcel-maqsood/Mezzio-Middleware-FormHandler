<?php
declare(strict_types=1);

namespace depa\FormularHandlerMiddleware;


interface AdapterInterface
{
    /**
     * Soll die Daten basierend auf dem jeweiligen Adapter verarbeiten
     * @return mixed
     */
    public function handleData();
}