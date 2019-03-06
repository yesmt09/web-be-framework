<?php

/**
 * @SWG\Swagger(
 *   schemes={"http","https"},
 *   host="",
 *   basePath="/api/v1",
 *   @SWG\Info(
 *     title="平台用户接口",
 *     version="1.0.0"
 *   )
 * )
 * )
 * @SWG\Tag(
 *   name="user",
 *   description="用户相关",
 * )
 *  @SWG\Tag(
 *   name="username authentication",
 *   description="用户名认证相关"
 * )
 * @SWG\Tag(
 *   name="mobile authentication",
 *   description="手机号认证相关"
 * )
 * @SWG\Tag(
 *   name="sso authentication",
 *   description="第三方平台登陆方式"
 * )
 */
$app->group(
    '/api/v1', function () use ($app, $container) {
    }
);

$app->get('/status', function (\Slim\Http\Request $req, \Slim\Http\Response $res) {
    return $res->withJson([
        'error'      => 0,
        'message'    => 'ok',
        'data'       => [],
        'timestamp'  => time(),
        'csrf'       => [],
        'session_id' => null,
    ]);
});
