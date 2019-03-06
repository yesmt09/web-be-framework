<?php

namespace app\middleware;

use Logger\Logger;
use Slim\Http\Request;
use Slim\Http\Response;

class NormalMiddleware extends Middleware
{
    public function __invoke(Request $request, Response $response, $next)
    {
        $pid = $request->getParam('pid', null);
        $uuid = $request->getParam('uuid', null);
        if (empty($pid) || empty($uuid)) {
            $this->setError(-1);
            $this->setMessage(__CLASS__.' error');
            Logger::warning(__CLASS__.'error');

            return $this->render($response);
        }

        return $next($request, $response);
    }
}
