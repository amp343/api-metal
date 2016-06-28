<?php

namespace ApiMetal\Error;

class UnprocessableEntity extends Error
{
    public function __construct($message = 'Unprocessable entity')
    {
        parent::__construct($message, 422);
    }
}
