<?php

namespace ApiMetal\Request;

use ApiMetal\Response\Response;
use ApiMetal\Route\MetalRouter;

class Handler
{
    public static function handleRequest(array $routes, array $server, array $params, string $basePath = null)
    {
        try {
            $request = new Request($server, $params);

            if ($basePath) {
                MetalRouter::preCheck404($request, $basePath);
            }

            MetalRouter::handleRequest($request, $routes);
        } catch (\Exception $e) {
            // @codeCoverageIgnoreStart
            self::handleError($e);
            // @codeCoverageIgnoreEnd
        }
    }

    public static function handleError(\Exception $e)
    {
        // @codeCoverageIgnoreStart
        ob_clean();
        $response = (new Response)->setError($e);
        $response->respond();
        // @codeCoverageIgnoreEnd
    }
}
