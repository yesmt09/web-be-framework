<?php
namespace console;

use app\components\Room;
use Logger\Logger;

// 定义根路径
defined('_ROOT') ? null : define('_ROOT', dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR);

// 设置时区
date_default_timezone_set('Asia/Shanghai');


foreach ($argv as $key=>$v) {
    if ($v ==='-p') {
        $_SERVER['ENVIRONMENT'] = $argv[$key + 1];
    }
}
// 载入自动载入类
$autoload = require _ROOT . '/vendor/autoload.php';

//require _ROOT . '/app/lib/helper.php';

class Console
{
    //帮助信息
    const USAGE
        = 'usage: php ROOT/lib/ScriptRunner.php -f -h -e -d
            其中各参数含义如下：
            -f 指定需要运行的脚本文件名
            -h 打印本段内容
            -e 运行环境
            -d 日志级别';

    //配置文件
    public $config;

    //脚本结束运行时间
    private $endTime;

    //脚本开始运行时间
    private $beginTime;

    //输入的参数
    private $arrOption;

    //日志级别
    private $logLevel = Logger::L_INFO;

    //运行脚本名称
    private $scriptName;

    private $env;

    public function __construct()
    {
        $settings = require _ROOT . '/app/settings.php';
        //
        // 启动设置
        $container = new \Slim\Container($settings);
        $settings['logger']();
        $this->_beginTime = microtime(true);
        $this->config = $container->get('config');
        $this->redis  = $container->get('redis');
        $this->room  = new Room($this->redis);
    }

    public function main($argv)
    {
        $this->initOptions($argv);
        //$this->defaultInit();
        $this->initLogger();
        $clzz = ('\console\\' . $this->scriptName);
        call_user_func_array(
            array(new $clzz(), 'executeScript'),
            array($this->arrOption['args'])
        );
        $this->end();
    }

    private function initOptions($argv)
    {
        $arrOption = self::getOption($argv, 'f::h::e::d:', 1);
        if (isset($arrOption ['h']) || !isset($arrOption ['f'])) {
            $this->printHelper();

            return null;
        }

        $file = dirname(__FILE__) . '/' . $arrOption ['f'];
        if (pathinfo($file, PATHINFO_EXTENSION) != 'php') {
            $file = dirname(__FILE__) . '/' . $arrOption ['f'] . '.php';
        }
        if (!file_exists($file)) {
            $file = $arrOption ['f'];
            if (!file_exists($file)) {
                self::printHelper("file $file not found");

                return null;
            }
        } else {
            require_once($file);
        }
        $this->scriptName = $arrOption['f'];

        if (isset($arrOption['e'])) {
            $_SERVER[Environment::ENV_VAR] = $arrOption['e'];
        }

        if (isset($arrOption['d'])) {
            $logLevelArray = array_change_key_case(
                array_flip(Logger::getLogLevel())
            );
            $this->_logLevel = isset($logLevelArray[$arrOption['d']])
                ? $logLevelArray[$arrOption['d']] : Logger::L_INFO;
        }

        return $this->arrOption = $arrOption;
    }

    /**
     * 根据参数解析输入配置
     *
     * @param array $arrArg
     *
     * @return array
     */
    protected static function getOption($arrArg, $option, $offset = 0)
    {
        $arrOption = array();
        for ($counter = 0; $counter < strlen($option); $counter++) {
            $config = $option [$counter];
            if ($config === ':') {
                Logger::fatal('invalid option:%s at %d', $option, $counter);
                throw new \Exception('config');
            }

            $arrOption [$config] = 1;
            if (isset($option [$counter + 1]) && $option [$counter + 1] === ':') {
                $counter++;
                $arrOption [$config] = 2;
                if (isset($option [$counter + 1])
                    && $option [$counter + 1] === ':'
                ) {
                    $counter++;
                    $arrOption [$config] = 3;
                }
            }
        }

        $arrArg = array_merge($arrArg);
        $arrRet = array(
            'args' => array(),
        );
        for ($counter = $offset; $counter < count($arrArg); $counter++) {
            $config = trim($arrArg [$counter]);
            if ($arrArg [$counter] [0] === '-') {
                $config = trim($config, '-');
                if (!isset($arrOption [$config])) {
                    $arrRet ['args'] [] = $arrArg [$counter];
                    continue;
                }

                switch ($arrOption [$config]) {
                    case 1:
                        self::addConfig($arrRet, $config, true);
                        break;
                    case 2:
                        $counter++;
                        if (!isset($arrArg [$counter])
                            || $arrArg [$counter] [0] == '-'
                        ) {
                            Logger::fatal("option %s requires arg", $config);
                            throw new \Exception('config');
                        }

                        self::addConfig($arrRet, $config, $arrArg [$counter]);
                        break;
                    case 3:
                        if (isset($arrArg [$counter + 1])
                            && $arrArg [$counter + 1] [0] != '-'
                        ) {
                            $counter++;
                            self::addConfig(
                                $arrRet,
                                $config,
                                $arrArg [$counter]
                            );
                        } else {
                            self::addConfig($arrRet, $config, true);
                        }
                        break;
                    default:
                        Logger::fatal(
                            "undefined option type:%d",
                            $arrOption [$config]
                        );
                        throw new \Exception('config');
                }
            } else {
                $arrRet ['args'] [] = $config;
            }
        }

        return $arrRet;
    }

    /**
     * 增加一个配置
     *
     * @param array  $arrConfig
     * @param string $config
     * @param string $value
     */
    private static function addConfig(&$arrConfig, $config, $value)
    {
        if (isset($arrConfig [$config])) {
            if (is_array($arrConfig [$config])) {
                $arrConfig [$config] [] = $value;
            } else {
                $arrConfig [$config] = array(
                    $arrConfig [$config],
                    $value,
                );
            }
        } else {
            $arrConfig [$config] = $value;
        }
        unset($arrConfig);
    }

    private function printHelper($message = self::USAGE)
    {
        print_r($message);
        return null;
    }

    private function defaultInit()
    {
        //        $env = new \app\lib\environment\Environment();
        // 载入设置
        $settings = require _ROOT . '/app/settings.php';
        //
        // 启动设置
        $container = new \Slim\Container($settings);
        $settings['logger']();
        $this->_beginTime = microtime(true);
        $this->config = $container->get('config');
        $this->redis  = $container->get('redis');
    }

    protected function initLogger()
    {
        $logPath = is_file($this->config['logger']['path']) ? dirname(
            $this->config['logger']['path']
        ) : $this->config['logger']['path'];
        Logger::init($logPath . '/script.log', $this->logLevel);
        Logger::addBasic('logid', Logger::getLogId());
        Logger::addBasic('scriptName', $this->scriptName);
    }

    private function end()
    {
        $this->_endTime = microtime(true);
        $totalCost = intval(($this->endTime - $this->beginTime) * 1000);
        Logger::notice("total cost: %d", $totalCost);
    }
}

$console = new console();
$console->main($argv);
