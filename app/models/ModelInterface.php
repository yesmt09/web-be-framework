<?php

namespace app\models;

/**
 * Interface ModelInterface
 *
 * @package app\models
 */
interface ModelInterface
{
    /**
     *  返回标名称
     *
     * @return string
     */
    public function getTableName();

    /**
     * 设置字段key
     *
     * @return array
     */
    public function attributeLabels();

    /**
     * 根据key查询数据
     *
     * @return array|null
     */
    public function find();

    public function findById($id);
}
