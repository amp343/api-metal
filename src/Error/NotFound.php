<?php

namespace ApiMetal\Error;

class NotFound extends Error
{
    public function __construct($message = 'Not found')
    {
        parent::__construct($message, 404);
    }
}
