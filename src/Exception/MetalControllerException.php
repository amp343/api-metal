<?php

namespace ApiMetal\Exception;

class MetalControllerException extends MetalException
{
    public function __construct($message = '')
    {
        $message = '| MetalControllerException - ' . $message;
        parent::__construct($message);
    }
}
