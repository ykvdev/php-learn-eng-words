<?php

namespace Lib;

/**
 * Class Mysql
 * @package Lib
 * About PHP PDO: http://phpfaq.ru/pdo
 */
class Mysql {
    /** @var array */
    private $config;

    /** @var \PDO */
    private $pdo;

    /**
     * @param array $config
     */
    public function __construct($config) {
        $this->config = $config;

        $this->connect();
    }

    public function beginTransaction()
    {
        $this->pdo->beginTransaction();
    }

    public function rollbackTransaction()
    {
        $this->pdo->rollBack();
    }

    public function commitTransaction()
    {
        $this->pdo->commit();
    }

    /**
     * @param string $sql
     * @param array|string $params
     * @return \PDOStatement
     */
    public function query($sql,$params = [])
    {
        if(!is_array($params)) {
            $params = array_slice(func_get_args(), 1);
        }

        $params = array_map(function($param){
            return ($param instanceof \DateTime) ? date_format($param, 'Y-m-d H:i:s') : $param;
        }, $params);

        $stm = $this->pdo->prepare($sql);
        $stm->execute($params);
        return $stm;
    }

    /**
     * @param string $tableName
     * @param int|string|array|null $idOrConditions
     * @return array|null
     * @throws \Exception
     */
    public function find($tableName, $idOrConditions) {
        $idOrConditions = null == $idOrConditions ? 0 : $idOrConditions;
        $result = $this->findAll($tableName, $idOrConditions, null, 1);
        return $result ? $result[0] : null;
    }

    /**
     * @param string $tableName
     * @return array|null
     */
    public function findRandom($tableName) {
        $sql = 'select count(*) from ' . $tableName;
        $totalCount = $this->fetchOne($sql);
        $offset = rand(0, $totalCount - 1);

        $sql = "select * from {$tableName} limit {$offset}, 1";
        return $this->fetchRow($sql);
    }

    /**
     * @param string $tableName
     * @param int|string|array|null $idOrConditions
     * @param string|null $orderBy
     * @param string|int|array|null $limit
     * @return array|null
     * @throws \Exception
     */
    public function findAll($tableName, $idOrConditions = null, $orderBy = null, $limit = null) {
        $sql = "select * from {$tableName}";

        if(is_string($idOrConditions) || is_numeric($idOrConditions)) {
            $primaryColumnName = $this->getPrimaryColumnOfTable($tableName);
            $sql .= " where `{$primaryColumnName}` = ? ";
        } elseif(is_array($idOrConditions)) {
            $sql .= " where ";
            if(strstr(key($idOrConditions), ' ') !== false) {
                $sql .= implode(" and ", array_keys($idOrConditions));
            } else {
                $conds = [];
                foreach($idOrConditions as $fieldName => $value) {
                    $conds[] = "{$fieldName} = ?";
                }
                $sql .= implode(" and ", $conds);
            }
            $idOrConditions = array_values($idOrConditions);
        }

        if($orderBy) {
            $sql .= " ORDER BY {$orderBy} ";
        }

        if($limit) {
            $limit = is_array($limit) ? implode(',', $limit) : $limit;
            $sql .= " LIMIT {$limit} ";
        }

        return $this->fetchAll($sql, $idOrConditions);
    }

    /**
     * @param string $sql
     * @param string $params
     * @return array
     */
    public function fetchAll($sql, $params = null) {
        if(!is_array($params)) {
            $params = array_slice(func_get_args(), 1);
        }

        $params = array_filter($params, function($value){
            return $value !== null;
        });

        return $this->query($sql, $params)->fetchAll();
    }

    public function fetchAllWithIdKeys($sql, $params = null) {
        if(!is_array($params)) {
            $params = array_slice(func_get_args(), 1);
        }
        $result = $this->query($sql, $params)->fetchAll();

        $resultWithIdKeys = [];
        foreach($result as $row) {
            $resultWithIdKeys[current($row)] = $row;
        }
        return $resultWithIdKeys;
    }

    /**
     * @param string $sql
     * @param null $params
     * @return array
     */
    public function fetchColumn($sql, $params = null) {
        if(!is_array($params)) {
            $params = array_slice(func_get_args(), 1);
        }
        $stm = $this->query($sql, $params);
        $result = [];
        while($value = $stm->fetchColumn()) {
            $result[] = $value;
        }
        return $result;
    }

    /**
     * @param string $sql
     * @param mixed $params
     * @return string
     */
    public function fetchOne($sql, $params = null) {
        if(!is_array($params)) {
            $params = array_slice(func_get_args(), 1);
        }
        return $this->query($sql, $params)->fetchColumn();
    }

    /**
     * @param string $sql
     * @param mixed $params
     * @return array|null
     */
    public function fetchRow($sql, $params = null) {
        if(!is_array($params)) {
            $params = array_slice(func_get_args(), 1);
        }
        $results = $this->query($sql, $params)->fetchAll();
        return $results ? $results[0] : null;
    }

    /**
     * @param string $tableName
     * @param array $data
     * @return int
     */
    public function insert($tableName, $data) {
        $set = [];
        $params = [];
        foreach ($data as $fieldName => $value) {
            $set[] = "`{$fieldName}` = ?";
            $params[] = $value;
        }
        $setStr = implode(', ', $set);

        $sql = "INSERT INTO `{$tableName}` SET {$setStr}";

        $this->query($sql, $params);

        return $this->pdo->lastInsertId();
    }

    /**
     * @param string $tableName
     * @param array $data
     * @param string $whereString
     * @param mixed $paramsForWhereString
     * @return int
     */
    public function update($tableName, $data, $whereString = null, $paramsForWhereString = null) {
        if(!is_array($paramsForWhereString)) {
            $paramsForWhereString = array_slice(func_get_args(), 3);
        }

        $set = [];
        $paramsForData = [];
        foreach ($data as $fieldName => $value) {
            $set[] = "`{$fieldName}` = ?";
            $paramsForData[] = $value;
        }
        $setStr = implode(', ', $set);

        $sql = "UPDATE `{$tableName}` SET {$setStr}";
        if($whereString) {
            $sql .= " WHERE {$whereString}";
        }

        $stm = $this->query($sql, array_merge($paramsForData, $paramsForWhereString));

        return $stm->rowCount();
    }

    /**
     * @param string $tableName
     * @param string $whereString
     * @param mixed $paramsForWhereString
     * @return int
     */
    public function delete($tableName, $whereString = null, $paramsForWhereString = null) {
        if(!is_array($paramsForWhereString)) {
            $paramsForWhereString = array_slice(func_get_args(), 2);
        }

        $sql = "DELETE FROM `{$tableName}`";
        if($whereString) {
            $sql .= " WHERE {$whereString}";
        }

        return $this->query($sql, $paramsForWhereString)->rowCount();
    }

    private function connect() {
        $dsn = "mysql:host={$this->config['host']};dbname={$this->config['dbname']};charset={$this->config['charset']}";
        $opt = array(
            \PDO::ATTR_ERRMODE              => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE   => \PDO::FETCH_ASSOC,
            \PDO::ATTR_EMULATE_PREPARES     => false,
        );
        $this->pdo = new \PDO($dsn, $this->config['user'], $this->config['password'], $opt);
    }

    private function getPrimaryColumnOfTable($tableName) {
        $sql = "SHOW KEYS FROM {$tableName} WHERE Key_name = 'PRIMARY'";
        return $this->fetchRow($sql)['Column_name'];
    }
}