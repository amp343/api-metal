<?php

namespace ApiMetal\Error;

class Forbidden extends Error
{
    public function __construct($message = 'Forbidden')
    {
        parent::__construct($message, 403);
    }
}
