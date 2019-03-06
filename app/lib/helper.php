<?php

/**
 * 检测参数并返回
 *
 * @param        $arrRequest
 * @param        $key
 * @param string $type
 * @param string $strict
 *
 * @return array|bool|float|int|null|string
 * @throws ErrException
 */
function getParameter($arrRequest, $key, $type = 'string', $strict = 'yes')
{
    if (!isset($arrRequest [$key])
        || (empty($arrRequest[$key])
            && $arrRequest[$key] != 0)
    ) {
        if ($strict === 'yes') {
            throw new Exception('参数错误:'.$key, 195);
        }

        return null;
    }
    $value = $arrRequest [$key];
    switch ($type) {
        case 'int':
            return intval($value);
        case 'string':
            return trim($value);
        case 'bool':
            return !empty($value);
        case 'float':
            return floatval($value);
        case 'array':
            return is_array($value) ? $value : array();
        case 'json':
            return json_decode($value, true) ? json_decode($value, true): [];
        default:
            throw new Exception('inter');
    }
}

/**
 * 批量转字符串
 *
 * @param $data
 *
 * @return array|string
 */
function dataToString($data)
{
    if (is_array($data)) {
        foreach ($data as $key => $value) {
            $data[strval($key)] = dataToString($value);
        }
    } else {
        $data = strval($data);
    }

    return $data;
}

/**
 * 获取客户端IP
 *
 * @return mixed
 */
function getClientIp()
{
    $client = new \GetClientIP\GetClientIP();

    return $client->getClientIp();
}

/**
 * 调试
 */
if (!function_exists('d')) {
    function d($param)
    {
        var_dump($param);
        exit();
    }
}

/**
 * 随机字符串
 *
 * @param int    $length
 * @param string $keyspace
 *
 * @return string
 */
function rand_str($length = 8, $keyspace = 'abcdefghijklmnopqrstuvwxyz234567')
{
    $str = '';
    $keysize = strlen($keyspace) - 1;
    for ($i = 0; $i < $length; ++$i) {
        $str .= $keyspace[mt_rand(0, $keysize)];
    }

    return $str;
}

/**
 * 随机数字
 *
 * @param int $length
 *
 * @return string
 */
function rand_int($length = 4)
{
    $codeStr = '';
    for ($i = 1; $i <= $length; $i++) {
        $codeStr .= mt_rand(0, 9);
    }

    return $codeStr;
}

/**
 * 获取身份证信息
 *
 * @param $IDCard
 *
 * @return mixed
 */
function getIDCardInfo($IDCard)
{
    $result['error'] = 0;//0：未知错误，1：身份证格式错误，2：无错误
    $result['isAdult'] = 1;//0标示成年，1标示未成年
    if (1 != preg_match("/^\d{15}$|^\d{17}X|x$|^\d{18}$/", $IDCard)) {
        $result['error'] = 1;

        return $result;
    } else {
        $err = 1;
        if (strlen($IDCard) == 18) {
            $tyear = intval(substr($IDCard, 6, 4));
            $tmonth = intval(substr($IDCard, 10, 2));
            $tday = intval(substr($IDCard, 12, 2));
            if ($tyear > date("Y") || $tyear < (date("Y") - 100) || $tmonth < 0 || $tmonth > 12 || $tday < 0 || $tday > 31) {
                $flag = 0;
            } else {
                $err = 0;
                $flag = 0;
                if ((date('Y') - $tyear) > 18) {
                    $flag = 1;
                } elseif ((date('Y') - $tyear) == 18) {
                    if (date('m') > $tmonth) {
                        $flag = 1;
                    } elseif (date('m') == $tmonth) {
                        if (date('d') >= $tday) {
                            $flag = 1;
                        }
                    }
                }
            }
        } elseif (strlen($IDCard) == 15) {
            $tyear = intval("19".substr($IDCard, 6, 2));
            $tmonth = intval(substr($IDCard, 8, 2));
            $tday = intval(substr($IDCard, 10, 2));
            if ($tyear > date("Y") || $tyear < (date("Y") - 100) || $tmonth < 0 || $tmonth > 12 || $tday < 0 || $tday > 31) {
                $flag = 0;
            } else {
                $err = 0;
                $flag = 0;
                if ((date('Y') - $tyear) > 18) {
                    $flag = 1;
                } elseif ((date('Y') - $tyear) == 18) {
                    if (date('m') > $tmonth) {
                        $flag = 1;
                    } elseif (date('m') == $tmonth) {
                        if (date('d') >= $tday) {
                            $flag = 1;
                        }
                    }
                }
            }
        }
    }
    $result['error'] = $err;//0：未知错误，1：身份证格式错误，2：无错误
    $result['isAdult'] = $flag;//1标示成年，0标示未成年

    return $result;
}
