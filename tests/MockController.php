<?php

namespace ApiMetal\Tests;

use ApiMetal\Controller\MetalController;
use ApiMetal\Error\Forbidden;

class MockController extends MetalController
{
    public function goodMethod()
    {
        return 'abc';
    }

    public function exceptionMethod()
    {
        throw new Forbidden('this is forbidden');
    }
}
