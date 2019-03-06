<?php

namespace app\controllers;

use Gregwar\Captcha\CaptchaBuilder;
use Logger\Logger;

/**
 * 控制器基础
 *
 * @author machao
 *
 */
trait Controller
{
    protected $container;
    protected $config;
    protected $cache;
    protected $session;
    protected $csrf;
    protected $csrf_name;
    protected $csrf_value;
    protected $req;
    protected $res;
    protected $error = false;
    protected $message = '';

    //返回值
    private $result
        = [
            'error'      => 1,
            'message'    => 'fail',
            'data'       => [],
            'timestamp'  => 0,
            'csrf'       => [],
            'session_id' => null,
        ];

    /**
     * Controller constructor.
     *
     * @param ContainerInterface $container
     *
     * @throws \Exception
     */
    public function __construct(ContainerInterface $container)
    {
        $this->init($container);
    }

    /**
     * 初始化信息
     *
     * @param ContainerInterface $container
     *
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function init(ContainerInterface $container)
    {
        $this->container = $container;
        $this->config = $this->container->get('config');
        $this->session = $this->container->get('session');
        $this->cache = $this->container->get('cache');

        $this->req = $this->container->get('request');
        $this->res = $this->container->get('response');

        $this->csrf = $this->container->get('csrf');
        $this->csrf_name = $this->csrf->getTokenNameKey();
        $this->csrf_value = $this->csrf->getTokenValueKey();
    }

    /**
     * 设置返回值提示语
     *
     * @param $message
     */
    private function setMessage($message)
    {
        $this->result['message'] = $message;
    }

    /**
     * 设置错误返回
     *
     * @param     $message
     * @param int $error
     */
    protected function setError($message, $error = 1)
    {
        $this->setMessage($message);
        $this->result['error'] = $error == 0 ? 1 : $error;
    }

    /**
     * 获取错误
     *
     * @return bool
     */
    protected function getError()
    {
        return $this->result['error'] !== 0;
    }

    /**
     * 设置成功信息
     *
     * @param $message
     */
    protected function setSuccess($message)
    {
        $this->setMessage($message);
        $this->result['error'] = 0;
    }

    /**
     * 设置返回值data
     *
     * @param array $arrData
     */
    public function setData(array $arrData)
    {
        $this->setSuccess('ok');
        if (empty($this->result['data'])) {
            $this->result['data'] = $arrData;
        } else {
            $this->result['data'] = array_merge(
                $this->result['data'], $arrData
            );
        }
    }

    /**
     * 返回值
     *
     * @return mixed
     */
    public function render()
    {
        $this->result['data'] = dataToString($this->result['data']);

        $this->result['error'] = intval($this->result['error']);
        $this->result['message'] = strval($this->result['message']);
        $this->result['timestamp'] = time();
        $this->result['session_id'] = $this->session->id();

        $this->result['csrf'] = [
            $this->csrf_name  => $this->req->getAttribute($this->csrf_name),
            $this->csrf_value => $this->req->getAttribute($this->csrf_value),

        ];
        Logger::info('result: %s', $this->result);
        $result = $this->result;
        $this->resetReuslt();
        return $this->res->withJson($result);
    }

    public function resetReuslt()
    {
        $this->result = [
            'error'      => 1,
            'message'    => 'fail',
            'data'       => [],
            'timestamp'  => 0,
            'csrf'       => [],
            'session_id' => null,
        ];
    }
}
