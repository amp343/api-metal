<?php

namespace MyAPI;

use ApiMetal\Controller\MetalController;

class Controller extends MetalController
{
    // provide a handler for the GET /numbers route

    public function getNumbers()
    {
        // always validate parameters.
        // doing so automatically handles validation for:
        // - parameter values vs the given type / validator
        // - parameter existence
        // - bad / not allowed parameters (any parameter not defined here)

        $this->validate([
            // route parameter
            'number' => ['required' => true, 'type' => 'int'],

            // optional query parameter. optional params should receive a default
            'favorite' => ['required' => false, 'type' => 'flag', 'default' => 0]
        ]);

        // return whatever should be delivered through the api.

        return [
          'number' => $this->getParam('number'), // ->getParam() gets the value of any known param
          'favorite' => $this->getParam('favorite') === 1
        ];
    }
}
