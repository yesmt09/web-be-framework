<?php

namespace app\middleware;

use app\controllers\Controller;
use Interop\Container\ContainerInterface;

class Middleware
{
    use Controller;

    public function __construct(ContainerInterface $container)
    {
        $this->init($container);
    }

    public function __invoke(Request $request, Response $response, $next)
    {
        return $next($request, $response);
    }
}
