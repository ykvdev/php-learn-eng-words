<?php

namespace Lib;

abstract class AbstractEntity
{
    /** @var  Mysql|string */
    protected $db = 'mysqldb';
    protected $tableName;
    protected $primaryKey = 'id';
    protected $fieldsMap;

    /**
     * @return int
     */
    public static function getCountRows()
    {
        $entity = new static;
        return (int)$entity->getDbAdapter()
            ->fetchOne("SELECT COUNT({$entity->getPrimaryKey()}) FROM {$entity->getTableName()}");
    }

    /**
     * @param int|string|array|null $idOrConditions
     * @param string|null $orderBy
     * @return $this
     * @throws \Exception
     */
    public static function find($idOrConditions = null, $orderBy = null)
    {
        $entity = new static();

        $data = $entity->getDbAdapter()->find($entity->getTableName(), $idOrConditions, $orderBy);
        if ($data) {
            return $entity->initFromArray($data);
        } else {
            return null;
        }
    }

    /**
     * @return $this
     */
    public static function findRandom() {
        $entity = new static();

        $data = $entity->getDbAdapter()->findRandom($entity->getTableName());
        if ($data) {
            return $entity->initFromArray($data);
        } else {
            return null;
        }
    }

    /**
     * @param int|string|array|null $idOrConditions
     * @param string|null $orderBy
     * @param string|null $limit
     * @return array|null
     * @throws \Exception
     */
    public static function findAll($idOrConditions = null, $orderBy = null, $limit = null)
    {
        $entity = new static();

        $data = $entity->getDbAdapter()->findAll($entity->getTableName(), $idOrConditions, $orderBy, $limit);
        foreach ($data as &$row) {
            $row = (new static())->initFromArray($row);
        }
        return $data;
    }

    /**
     * @param array $array
     * @return $this
     */
    public function initFromArray($array)
    {
        foreach ($array as $field => $value) {
            if (property_exists($this, $field)) {
                $this->$field = $value;
            }
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function save()
    {
        if (self::find($this->{$this->getPrimaryKey()})) {
            $this->update();
        } else {
            $this->create();
        }

        return $this;
    }

    public function update()
    {
        $this->getDbAdapter()->update(
            $this->getTableName(),
            $this->toArray(),
            "{$this->getPrimaryKey()} = ?",
            $this->{$this->getPrimaryKey()}
        );

        return $this;
    }

    public function create()
    {
        $this->{$this->getPrimaryKey()} = $this->getDbAdapter()->insert(
            $this->getTableName(),
            $this->toArray()
        );

        return $this;
    }

    public function remove()
    {
        $this->getDbAdapter()->delete(
            $this->getTableName(),
            "{$this->getPrimaryKey()} = ?",
            $this->{$this->getPrimaryKey()}
        );

        return $this;
    }

    /**
     * @return array
     */
    public function toArray()
    {
//        $tableFields = $this->getDbAdapter()->fetchColumn('SHOW COLUMNS FROM ' . $this->getTableName());

        $data = [];
        foreach ($this->getTableFields() as $fieldName) {
            if (property_exists($this, $fieldName) && !is_null($this->$fieldName)) {
                $value = $this->$fieldName === 'null' ? null : $this->$fieldName;
                $data[$fieldName] = $value;
            }
        }

        return $data;
    }

    /**
     * @return Mysql
     */
    public function getDbAdapter()
    {
        if (!$this->db instanceof Mysql) {
            if (!function_exists($this->db)) {
                throw new \LogicException("Invalid mysql db adapter");
            }
            $adapter = $this->db;
            $this->db = $adapter();
        }
        return $this->db;
    }

    /**
     * @return string
     */
    protected function getPrimaryKey()
    {
        return $this->primaryKey;
    }

    /**
     * @return string
     */
    protected function getTableName()
    {
        if (!$this->tableName) {
            throw new \LogicException(sprintf("Property name entity %s is empty", __CLASS__));
        }
        return $this->tableName;
    }

    /**
     * @return array
     */
    protected function getTableFields()
    {
        if (null == $this->fieldsMap) {
            $reflection = new \ReflectionClass($this);
            if ($reflection) {
                $this->fieldsMap = array_map(function (\ReflectionProperty $property) {
                    return $property->name;
                }, array_filter($reflection->getProperties(\ReflectionProperty::IS_PUBLIC), function (\ReflectionProperty $property) {
                    return $property->isStatic() === false;
                }));
            }
        }

        if (!is_array($this->fieldsMap) && empty($this->fieldsMap)) {
            throw new  \LogicException("Table field is empty or invalid");
        }

        return $this->fieldsMap;
    }
}