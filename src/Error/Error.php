<?php

namespace ApiMetal\Error;

class Error extends \Exception
{
    protected $internalCode;

    public function __construct($message, $status, $code = 0)
    {
        parent::__construct($message, $status);

        $this->internalCode = $code;
    }

    public function getInternalCode()
    {
        return $this->internalCode;
    }
}
