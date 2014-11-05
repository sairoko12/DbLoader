<?php

/**
 * The use this class is only for assemble easy queries for advanced options
 * please read more about the PDO in the next link: http://php.net/manual/es/class.pdo.php
 *
 */

class BuilderSql {
    private $camps;
    private $from;
    private $wheres;
    private $limit;
    private $orderBy;
    private $sortBy;
    private $groupBy;
    private $joins;
    private $driver;
    protected $db;
    
    public function __construct(PDO $db) {
        $this->db = $db;
        $this->driver = $this->db->getAttribute(PDO::ATTR_DRIVER_NAME);
        
        /**
         * Aqui se puede agregar nuevos cases para modificar los parametros que necesites para generar tus querys de acuerdo a la base de datos que estes utilizando.
         * Ejemplo: case 'oci': para oracle, case 'ibm': para DB2
        */
        switch ($this->driver) {
            case 'mysql': default: 
                $this->wheres = array('and' => array(), 'or' => array());
                $this->joins = array('inner' => array(), 'left' => array(), 'right' => array(), 'full' => array());
                break;
        }
    }
    
    public function fetchAll() {
        $query = $this->assemble();
        if ($query) {
            $sth = $this->db->prepare($query);
            $sth -> execute();
            
            $result = $sth -> fetchAll(PDO::FETCH_OBJ);
            
            return (is_array($result)) ? $result : false;
        }

        return false;
    }

    public function fetchRow() {
        $query = $this->assemble();
        
        if ($query) {
            $obj = $this->db->query($query);
            
            if (is_object($obj)) {
                $result = $obj->fetch(PDO::FETCH_OBJ);
                return (is_object($result)) ? $result : false;
            }
            
            return false;
        }

        return false;
    }

    public function select($camps = array('*')) {
        if (is_array($camps)) {
            $this->camps = implode(',', $camps);
        } else {
            $this->camps = $camps;
        }

        return $this;
    }

    public function from($table = array()) {
        if (is_array($table)) {
            $this->from = $table[key($table)] . " AS " . key($table);
        } else {
            $this->from = (!empty($table)) ? $table : false;
        }

        return $this;
    }

    public function innerJoin($table = array(), $condition = '') {
        if (is_array($table)) {
            $this->joins['inner'][] = "INNER JOIN " . $table[key($table)] . " AS " . key($table) . " ON " . $condition;
        } else {
            $this->joins['inner'][] = "INNER JOIN " . $table . " ON " . $condition;
        }

        return $this;
    }

    public function leftJoin($table = array(), $condition = '') {
        if (is_array($table)) {
            $this->joins['left'][] = "LEFT JOIN " . $table[key($table)] . " AS " . key($table) . " ON " . $condition;
        } else {
            $this->joins['left'][] = "LEFT JOIN " . $table . " ON " . $condition;
        }

        return $this;
    }

    public function rightJoin($table = array(), $condition = '') {
        if (is_array($table)) {
            $this->joins['right'][] = "RIGHT JOIN " . $table[key($table)] . " AS " . key($table) . " ON " . $condition;
        } else {
            $this->joins['right'][] = "RIGHT JOIN " . $table . " ON " . $condition;
        }

        return $this;
    }

    public function fullJoin($table = array(), $condition = '') {
        if (is_array($tables)) {
            $this->joins['full'][] = "FULL JOIN " . $table[key($table)] . " AS " . key($table) . " ON " . $condition;
        } else {
            $this->joins['full'][] = "FULL JOIN " . $table . " ON " . $condition;
        }

        return $this;
    }

    public function where($where) {
        $this->wheres['and'][] = $where;
        return $this;
    }

    public function orWhere($orWhere) {
        $this->wheres ['or'][] = $orWhere;
        return $this;
    }

    public function limit($limit) {
        $this->limit = $limit;
        return $this;
    }

    public function orderBy($camp, $order) {
        $this->orderBy = $camp;
        $this->sortBy = $order;
        return $this;
    }

    public function groupBy($camps = array()) {
        if (is_array($camps)) {
            $this->groupBy = implode(',', $camps);
        } else {
            $this->groupBy = $camps;
        }

        return $this;
    }

    public function insert($table, $data = array()) {
        $query = "INSERT INTO {$table}(" . implode(',', array_keys($data)) . ") VALUES(" . implode(',', array_map(function($value){ return (is_numeric($value)) ? "{$value}" : "'{$value}'"; }, $data)) . ");";
        $result = $this->db()->prepare($query);
        $result->execute();
        return $this->db()->lastInsertId();
    }

    public function array_map_assoc($callback, $array) {
        $r = array();

        foreach ($array as $key => $value) {
            $r[$key] = $callback($key, $value);
        }

        return $r;
    }

    public function update($table, $data, $condition = '') {
        $query = "UPDATE {$table} SET " . implode(',', $this->array_map_assoc(function($k, $v){ return (is_numeric($v)) ? "{$k} = {$v}" : "{$k} = '{$v}'"; }, $data));
        
        if ($condition !== '') {
            $query .= " WHERE " . $condition;
        }

        $query .= ";";
        
        $result = $this->db()->prepare($query);
        
        try {
            return $result->execute();
        } catch (PDOException $e) {
            return $e->getMessage();
        }
    }

    public function delete($table, $data) {
        $query = "DELETE FROM {$table} WHERE " . implode(' AND ', $this->array_map_assoc(function($k, $v){ return (is_numeric($v)) ? "({$k} = {$v})" : "({$k} = '{$v}')"; }, $data)) . ";";
        
        try {
            return $this->db->exec($query);
        } catch (PDOException $e) {
            return $e->getMessage();
        }
    }

    public function assemble() {
        if (empty($this->camps) || $this->from === false) {
            return false;
        }

        $query = "SELECT " . $this->camps . " FROM " . $this->from . ' ';

        //Juntamos JOINS
        foreach ($this->joins AS $key => $value) {
            if (count($value)) {
                for ($i = 0; $i < count($value); $i++) {
                    $query .= $value[$i] . ' ';
                }
            }
        }

        //Agregamos condciones
        if (count($this->wheres['and']) || count($this->wheres['or'])) {
            $query .= 'WHERE ';

            $ands = 0;
            $ors = 0;

            foreach ($this->wheres AS $key => $value) {
                if (count($value)) {
                    switch ($key) {
                        case 'and':
                            $ands ++;

                            if ($ors > 0) {
                                $query .= ' AND ';
                            }

                            $query .= implode(' AND ', $value);
                            break;
                        case 'or':
                            $ors ++;

                            if ($ands > 0) {
                                $query .= ' OR ';
                            }

                            $query .= implode(' OR ', $value);
                            break;
                    }
                }
            }
        }

        //Agregamos groupBy
        if (strlen($this->groupBy)) {
            $query .= " GROUP BY {$this->groupBy}";
        }

        //Agregamos orderBy
        if (!empty($this->orderBy) && !empty($this->sortBy)) {
            $query .= " ORDER BY {$this->orderBy} {$this->sortBy}";
        }

        //Agregamos limit
        if (!empty($this->limit) && is_numeric($this->limit) && intval($this->limit) > 0) {
            $query .= " LIMIT {$this->limit}";
        }

        $query .= ";";

        return $query;
    }
    
    public function db(){
        return $this->db;
    }
}
