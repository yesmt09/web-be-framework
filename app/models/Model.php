<?php

namespace app\models;

use CData\CData;
use Logger\Logger;
use app\config\code;

/**
 * Class Model
 *
 * @package app\models
 */
abstract class Model extends ModelHelper implements ModelInterface
{

    /**
     * CData对象
     *
     * @var CData
     */
    protected $data;
    protected $key;

    /**
     *  数据库
     *
     * @var string
     */
    protected $database = 'xxxx';

    public function __construct()
    {
        $this->data = new CData();
        $this->data->useDb($this->database);
    }

    /**
     * 设置数据库
     * @param $database
     */
    public function setDatabase($database)
    {
        $this->database = $database;
    }

    /**
     * select 数据
     *
     * @param null $arrSelect
     *
     * @return CData
     * @throws \Exception
     */
    protected function select($arrSelect = null)
    {
        if (empty($arrSelect)) {
            $arrSelect = array_keys($this->attributeLabels());
        }
        $this->data->useDb($this->database);

        return $this->data->select($arrSelect)->from($this->getTableName());
    }

    /**
     * Count 数据
     *
     * @return CData
     */
    protected function selectCount()
    {
        $this->data->useDb($this->database);

        return $this->data->selectCount($this->getTableName());
    }

    /**
     * 更新数据
     *
     * @param array $arrUpdate
     * @param array $where
     *
     * @return array
     * @throws \CData\如果command不合法
     * @throws \Exception
     */
    public function update(array $arrUpdate, array $where = [])
    {
        $this->data->useDb($this->database);
        $this->data->update($this->getTableName());

        if (empty($where)) {
            $where = $this->getWhere();
            $this->data->where($where);
        } elseif (is_array($where[0])) {
            foreach ($where as $key => $value) {
                $this->data->where($value);
            }
        }

        $set = [];
        foreach ($arrUpdate as $key => $value) {
            if (!isset($this->attributeLabels()[$key])) {
                continue;
            }
            $set[$key] = $value;
        }
        $result = $this->data->set($set)->query();

        return ($result['affected_rows'] === 1);
    }

    /**
     * 插入或更新数据
     *
     * @return CData
     */
    protected function insertOrUpdate()
    {
        $this->data->useDb($this->database);

        return $this->data->insertOrUpdate($this->getTableName());
    }

    /**
     * 插入数据
     *
     * @return CData
     */
    protected function insertIgnore(array $insertValue)
    {
        $this->data->useDb($this->database);

        $result = $this->data->insertIgnore($this->getTableName())->values($insertValue)->query();

        return ($result['affected_rows'] === 1);
    }

    /**
     * 插入数据
     *
     * @return CData
     */
    protected function insertInto(array $insertValue)
    {
        $this->data->useDb($this->database);

        $result = $this->data->insertInto($this->getTableName())->values($insertValue)->query();

        return ($result['affected_rows'] === 1);
    }

    /**
     * 插入数据
     *
     * @return CData
     */
    protected function delete()
    {
        $this->data->useDb($this->database);

        return $this->data->deleteFrom($this->getTableName());
    }

    /**
     * 插入或更新数据
     *
     * @return array
     * @throws \CData\如果command
     * @throws \CData\如果command不合法
     * @throws \Exception
     */
    public function save()
    {
        $update = [];
        foreach ($this as $key => $value) {
            if ((empty($value) && $value !== 0) || !isset($this->attributeLabels()[$key])) {
                continue;
            }
            $update[$key] = $value;
        }

        return $this->insertOrUpdate()->values($update)->onDuplicateUpdateKey($this->key)->query();
    }

    /**
     * 获取是否设置了model 需要的key
     *
     * @param $key
     *
     * @return null
     * @throws ModelException
     */
    protected function getKey()
    {
        $key = $this->key;
        if (empty($this->$key)) {
            Logger::warning('Model err: %s, %s', get_class($this), $this->key);
            $msg = code::$msgArr[code::USERNAME_OR_PASSWORD_ERROR];
            throw new ModelException($msg);
        }

        return $this->$key;
    }

    protected function getWhere()
    {
        return [$this->key, '==', $this->getKey()];
    }

    public function findById($id)
    {
        $queryResult = $this->select()->where([$this->key, '==', $id])->query();
        if (empty($queryResult[0])) {
            return [];
        }

        return $this->setParams($queryResult[0]);
    }
}
