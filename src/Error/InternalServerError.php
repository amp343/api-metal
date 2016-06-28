<?php

namespace ApiMetal\Error;

class InternalServerError extends Error
{
    public function __construct($message = 'Internal server error')
    {
        parent::__construct($message, 500);
    }
}
