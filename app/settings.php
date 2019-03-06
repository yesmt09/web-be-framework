<?php

use Logger\Logger;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * 返回slim框架需要的配置
 */


$config = Environment\Environment::getConfig(_ROOT.'app/config');


return [
    "config"   => $config,
    "settings" => [
    ],
    'cache'    => function ($c) use ($config) {
    },
    'session'  => function ($c) {
        return new \SlimSession\Helper;
    },

    'csrf'                              => function ($c) {
        return new \Slim\Csrf\Guard;
    },
    'logger'                            => function () use ($config) {
        return true;
    },
    'errorHandler'                      => function ($c) {
        return function (Request $request, Response $response,
            Exception $exception
        ) use ($c) {
            logger::fatal(
                'error, file: %s, line: %d, message: %s',
                $exception->getFile(),
                $exception->getLine(),
                $exception->getMessage()
            );

            $result = [
                'error'      => $exception->getCode() == 0 ? 1
                    : $exception->getCode(),
                'message'    => $exception->getMessage(),
                'data'       => array(),
                'timestamp'  => 0,
                'csrf'       => [],
                'session_id' => null,
            ];

            return $response->withJson($result);
        };
    },
    'phpErrorHandler'                   => function ($c) {
        return function (Request $request, Response $response, $error) use ($c
        ) {
            Logger::fatal($error->xdebug_message);
            $result = [
                'error'      => 502,
                'message'    => 'Error',
                'data'       => array(),
                'timestamp'  => 0,
                'csrf'       => [],
                'session_id' => null,
            ];

            return $response->withJson($result);
        };
    },
    'notFoundHandler' => function ($c) {
        return function (Request $request, Response $response) use ($c) {
            $result = [
                'error'      => 404,
                'message'    => 'not Found',
                'data'       => array(),
                'timestamp'  => 0,
                'csrf'       => [],
                'session_id' => null,
            ];

            return $response->withJson($result);
        };
    },
    'determineRouteBeforeAppMiddleware' => true,
];
