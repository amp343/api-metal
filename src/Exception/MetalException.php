<?php

namespace ApiMetal\Exception;

abstract class MetalException extends \Exception
{
    public function __construct($message = '')
    {
        $message = 'ApiMetal | MetalException ' . $message;
        parent::__construct($message);
    }
}
