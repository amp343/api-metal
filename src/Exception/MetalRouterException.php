<?php

namespace ApiMetal\Exception;

class MetalRouterException extends MetalException
{
    public function __construct($message = '')
    {
        $message = '| MetalRouterException - ' . $message;
        parent::__construct($message);
    }
}
