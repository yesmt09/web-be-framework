<?php

namespace app\controllers;

use Environment\Environment as env;
use PHPUnit\Framework\TestCase;
use Slim\Http\Response;

define('_PHPUNIT', true);
global $_SESSION, $_GET, $_POST;

class TestController extends TestCase
{
    /** @var Response */
    protected $response;

    public static $customParam;

    public $baseUrl = '/api/v1';
    public $url = '';
    public $requestParameters = [];

    public function setUp()
    {
        $_SESSION = [];
        self::getParam();
    }

    protected function tearDown()
    {
        $this->response = null;
    }

    protected function request($method, $url, array $requestParameters = [])
    {
        global $_GET, $_POST;
        unset($_SERVER['REQUEST_URI']);
        $_SERVER['REQUEST_URI'] = $this->baseUrl . $url;
        $_GET = $_POST = $requestParameters;
        $app = require dirname(TEST_ROOT) . '/public/index.php';
        $this->response = $app->subRequest(
            $method,
            $this->baseUrl . $url,
            http_build_query($requestParameters)
        );
    }

    protected function getResponseData()
    {
        $this->assertJson((string)$this->response->getBody());
        return json_decode((string)$this->response->getBody(), true);
    }

    protected function assertResponseOk($result)
    {
        $this->assertNotEmpty($result);
        $this->assertEquals($result['error'], 0, $result['message']);
        $this->assertNotEmpty($result['data']);
        return $result['data'];
    }

    public static function getParam()
    {
        global $argv;
        $_SERVER['ENVIRONMENT'] = 'development';
        foreach ($argv as $key => $value) {
            if ($value === '..env') {
                $_SERVER[env::getEnvName()] = $argv[$key + 1];
            }

            if ($value === '..param') {
                self::$customParam[] = $argv[$key + 1];
            }
        }
    }
}
