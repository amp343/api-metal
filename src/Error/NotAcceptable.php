<?php

namespace ApiMetal\Error;

class NotAcceptable extends Error
{
    public function __construct($message = 'Not acceptable')
    {
        parent::__construct($message, 406);
    }
}
