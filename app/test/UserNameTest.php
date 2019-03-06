<?php

namespace app\test;

defined('TEST_ROOT') ? null : define('TEST_ROOT', dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR);

// 载入自动载入类
$autoload = require './vendor/autoload.php';

use app\controllers\TestController;

class UserNameTest extends TestController
{
}
