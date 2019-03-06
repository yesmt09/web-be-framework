<?php
$_begin = microtime(true);

// 定义根路径

// 设置时区
date_default_timezone_set('Asia/Shanghai');

defined('_ROOT') ? null : define('_ROOT', dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR);

// 载入自动载入类
$autoload = require _ROOT . '/vendor/autoload.php';

// 载入设置
$settings = require _ROOT . '/app/settings.php';

$settings['logger']();

try {
    // 启动设置
    $container = new \Slim\Container($settings);

    $app = new \Slim\App($container);

    //    $app->add($container->get('csrf'));

    $app->add(new \Slim\Middleware\Session([
        'name' => 'babeltime_session',
        'autorefresh' => true,
        'lifetime' => '1 hour'
    ]));

    $app->get('/', function ($request, $response, $args) {
        $ipAddress = $request->getAttribute('ip_address');

        return $response;
    });

    // 载入路由
    require _ROOT . '/app/router.php';

    if (!defined('_PHPUNIT') || _PHPUNIT !== true) {
        // 启动框架
        $app->run();
    } else {
        return $app;
    }
} catch (Exception $e) {
    \Logger\Logger::fatal($e->getMessage());
}

$_end = microtime(true);
$totalCost = intval(($_end - $_begin) * 1000);
\Logger\Logger::notice("total cost: %d", $totalCost);
