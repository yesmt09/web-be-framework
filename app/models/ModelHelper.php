<?php

namespace app\models;

class ModelHelper
{
    public $field = [];

    /**
     * 根据字符串分表
     *
     * @param     $string
     * @param     $table
     * @param int $numOf
     *
     * @return string
     */
    public static function getTableNameByString($string, $table, $numOf = 100)
    {
        $str = substr(md5($string), 0, 5);
        $num = hexdec($str) % $numOf;

        return $table.$num;
    }

    /**
     * 根据数字进行分表
     *
     * @param     $int
     * @param     $table
     * @param int $numOf
     *
     * @return string
     */
    public static function getTableNameByInt($int, $table, $numOf = 100)
    {
        $num = $int % $numOf;

        return $table.$num;
    }

    /**
     * 设置参数
     *
     * @param array $queryResult
     *
     * @return array
     */
    protected function setParams(array $queryResult)
    {
        foreach ($this->attributeLabels() as $key => $value) {
            if (isset($queryResult[$key])) {
                $this->$key = $queryResult[$key];
                $this->field[$key] = $queryResult[$key];
            }
        }

        return $this;
    }

    /**
     * @param $name
     *
     * @return null
     */
    public function __get($name)
    {
        if (isset($this->$name)) {
            return $this->$name;
        } else {
            return null;
        }
    }

    /**
     * @param $name
     * @param $value
     */
    public function __set($name, $value)
    {
        $this->$name = $value;
    }
}
