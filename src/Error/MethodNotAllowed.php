<?php

namespace ApiMetal\Error;

class MethodNotAllowed extends Error
{
    public function __construct($message = 'Method not allowed')
    {
        parent::__construct($message, 405);
    }
}
