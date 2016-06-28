<?php

namespace ApiMetal\Error;

class ServiceUnavailable extends Error
{
    public function __construct($message = 'Service unavailable')
    {
        parent::__construct($message, 503);
    }
}
