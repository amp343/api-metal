<?php

require './vendor/autoload.php';

use ApiMetal\Request\Handler;

// define an array of Routes
// - http method
// - match string
// - handler: controller#method

$routes = [
    new Route('GET', 'myAPI/numbers/{number:\d+}', '\\MyAPI\\Controller#getNumbers')
];

// handleRequest will:
// - match the request to the given route
// - invoke the matched controller method
// - deliver the result of that controller method,
//   or an error if one occurs.

Handler::handleRequest($routes, $_SERVER, $_REQUEST);
