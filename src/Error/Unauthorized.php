<?php

namespace ApiMetal\Error;

class Unauthorized extends Error
{
    public function __construct($message = 'Unauthorized')
    {
        parent::__construct($message, 401);
    }
}
