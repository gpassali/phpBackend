<?php

namespace backend\abstracts;

use SqlEnum;

include_once 'app/enums/sql.php';
abstract class aMysql extends aDatabase
{

    private $db;
    protected $table;
    public $affected_rows;
    private $sql;

    function __construct()
    {
        //parent::__construct();
        if (isset($_SERVER['SERVER_NAME'])) {
            if ($_SERVER['SERVER_NAME'] == 'localhost' || $_SERVER['SERVER_NAME'] == 'clas1990.test') {
                $db = new \mysqli("", "root", "", DB);
            } else {
                $db = new \mysqli("localhost", 'root', "clas1990db", "clas1990");
            }
        } else {
            $db = new \mysqli("", "root", "", DB);
        }

        if ($db->connect_errno) {
            $this->setError(__FILE__, __LINE__, $this->db->error, "Connessione fallita", '00');
            return false;
        }
        $this->db = $db;
    }

    function getTable()
    {
        return $this->table;
    }

    function getSql()
    {
        return $this->sql;
    }

    function query($sql, SqlEnum $type = SqlEnum::select, $logga=true)
    {
        try {
            if($logga){
                $this->logger($sql, $type->value);
            }
            
            $this->sql = $sql;
            $res = $this->db->query($sql);
            if ($this->db->errno) {
                $this->setError(__FILE__, __LINE__, $this->db->error, $sql, '01');
                return false;
            }
            if(!$res){
                $this->setError(__FILE__, __LINE__, $this->db->error, $sql, '01');
                return false;
            }
        } catch (\Exception $e) {
            $this->setError(__FILE__, __LINE__, $e->getMessage(), $sql, '02');
            return false;
        }
        $this->affected_rows = $this->db->affected_rows;
        if ($type->value == 'SELECT') {
            $rows = [];
            while ($row = $res->fetch_assoc()) {
                $rows[] = $row;
            }
            return $rows;
        } else if ($type->value == 'INSERT') {
            return ['insert_id' => $this->db->insert_id];
        } else if ($type->value == 'DELETE') {
            return ['affected_rows' => $this->db->affected_rows];
        } else if ($type->value == 'UPDATE') {
            return ['affected_rows' => $this->db->affected_rows];
        }
    }

    function logger($sql, $tipo){
        $sql = $this->real_escape_string($sql);
        $tipo = $this->real_escape_string($tipo);
        $logSql = "INSERT INTO logger (`query`, tipo) VALUES ('$sql', '$tipo')";
        try {
            $res = $this->db->query($logSql);
            if ($this->db->errno) {
                $this->setError(__FILE__, __LINE__, $this->db->error, $logSql, '02');
                return false;
            }
        } catch (\Exception $e) {
            $this->setError(__FILE__, __LINE__, $e->getMessage(), $logSql, '03');
        }
    }

    function join($table, $on)
    {
        $onSql = '';
        if (is_array($on)) {
            $onSql = "on " . implode(' AND ', array_map(function ($v, $k) {
                return "$k = $v";
            }, $on, array_keys($on)));
        }
        return "left join $table $onSql";
    }

    function real_escape_string($str)
    {
        return $str ? $this->db->real_escape_string($str) : $str;
    }

    function escapeArray(array $data)
    {
        return array_map(function ($v) {
            return $v ? $this->real_escape_string($v) : $v;
        }, $data);
    }


    function select(array $where = [])
    {
        $sql = "SELECT * FROM $this->table";
        if (count($where) > 0) {
            $sql .= ' WHERE ';
            $sql .= implode(' AND ', array_map(function ($v, $k) {
                return "$k = '$v'";
            }, $where, array_keys($where)));
        }
        try {
            $this->sql = $sql;
            return $this->query($sql);
        } catch (\Exception $e) {
            $this->setError(__FILE__, __LINE__, $e->getMessage());
            return false;
        }
    }

    function insert(array $data)
    {
        $sql = "INSERT INTO $this->table (";
        $sql .= implode(',', array_keys($data));
        $sql .= ') VALUES (';
        $sql .= implode(',', array_map(function ($v) {
            $v = $this->real_escape_string($v);
            return "'$v'";
        }, $data));
        $sql .= ')';
        try {
            return $this->query($sql, SqlEnum::insert);
        } catch (\Exception $e) {
            $this->setError(__FILE__, __LINE__, $e->getMessage());
            return false;
        }
    }

    function insertDuplicate(array $data, array $duplicate)
    {
        $sql = "INSERT INTO $this->table (";
        $sql .= implode(',', array_keys($data));
        $sql .= ') VALUES (';
        $sql .= implode(',', array_map(function ($v) {
            return "'$v'";
        }, $data));
        $sql .= ') ON DUPLICATE KEY UPDATE ';
        $sql .= implode(',', array_map(function ($v, $k) {
            return "$k = '".$this->real_escape_string($v)."'";
        }, $duplicate, array_keys($duplicate)));
        $this->sql = $sql;
        try {
            return $this->query($sql, SqlEnum::insert);
        } catch (\Exception $e) {
            $this->setError(__FILE__, __LINE__, $e->getMessage());
            return false;
        }
    }


    function update(array $data, array $where)
    {
        $sql = "UPDATE $this->table SET ";
        $sql .= implode(',', array_map(function ($v, $k) {
            return "$k = '".$this->real_escape_string($v)."'";
        }, $data, array_keys($data)));
        $sql .= ' WHERE ';
        $sql .= implode(' AND ', array_map(function ($v, $k) {
            return "$k = '$v'";
        }, $where, array_keys($where)));
        $this->sql = $sql;
        try {
            return $this->query($sql, SqlEnum::update);
        } catch (\Exception $e) {
            $this->setError(__FILE__, __LINE__, $e->getMessage());
            return false;
        }
    }

    function delete(array $where)
    {
        $sql = "DELETE FROM $this->table WHERE ";
        $sql .= implode(' AND ', array_map(function ($v, $k) {
            return "$k = '$v'";
        }, $where, array_keys($where)));
        try {
            return $this->query($sql, SqlEnum::delete);
        } catch (\Exception $e) {
            $this->setError(__FILE__, __LINE__, $e->getMessage());
            return false;
        }
    }
}
