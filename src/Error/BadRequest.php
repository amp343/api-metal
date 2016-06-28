<?php

namespace ApiMetal\Error;

class BadRequest extends Error
{
    public function __construct($message = 'Bad request')
    {
        parent::__construct($message, 400);
    }
}
