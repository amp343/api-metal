<?php

namespace ApiMetal\Error;

class RequestTimeout extends Error
{
    public function __construct($message = 'Request timeout')
    {
        parent::__construct($message, 408);
    }
}
