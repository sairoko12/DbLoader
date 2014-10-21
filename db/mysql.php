<?php

class DatabaseMysql extends PDO implements DatabaseInterface {
    private $query;
    private $results;
    private $camps;
    private $from;
    private $wheres = array('and' => array(), 'or' => array());
    private $limit;
    private $orderBy;
    private $sortBy;
    private $groupBy;
    private $joins = array('inner' => array(), 'left' => array(), 'right' => array(), 'full' => array());
    protected $db;

    public function __construct($host, $username, $passwd, $database) {
        try {
            parent::__construct("mysql:host={$host};dbname={$database};", $username, $passwd);
            parent::setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true);
        } catch (PDOException $e) {
            throw new Exception($e->getMessage(), 500);
        }
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
            $this->from = $table;
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

    public function addWhere($where) {
        $this->wheres['and'][] = $where;
        return $this;
    }

    public function addOrWhere($orWhere) {
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

    public function fetchAll() {
        //Build the query
        $this->assemble();
        //If property is empty return false 
        if ($this->query !== '') {
            $sth = parent::prepare($this -> query);
            $sth -> execute();
            
            $result = $sth -> fetchAll(PDO::FETCH_OBJ);
            
            $this->clearQuery();
            
            return (is_array($result)) ? $result : false;
        }

        return false;
    }

    public function fetchRow() {
        //Build the query
        $this->assemble();
        //If property is empty return false 
        if ($this->query !== '') {
            $obj = parent::query($this->query);
            
            if (is_object($obj)) {
                $result = $obj->fetch(PDO::FETCH_OBJ);
                $this -> clearQuery();
                return (is_object($result)) ? $result : false;
            }
            
            return false;
        }

        return false;
    }

    public function insert($table, $camps = array(), $values = array(), $multiple = false) {
        if ($multiple){
            $records = array();
            foreach ($values AS $value) {
                foreach ($value AS &$val) {
                    $val = "'" . $val . "'";
                }
                
                $records[] .= "(" . implode(',',$value) . ")";
            }
        } else {
            foreach ($values AS &$val) {
                $val = "'" . $val . "'";
            }
        }
        
        $query = "INSERT INTO {$table} (" . implode(',',$camps) . ") VALUES (" . implode(',', (($multiple) ? $records : $values)) . ");";
        $result = parent::prepare($query);
        $result ->execute(array($table, implode(',',$camps)));
        return parent::lastInsertId();
    }

    public function update($table, $camp, $value, $condition = '') {
        $query = "UPDATE {$table} SET {$camp} = '{$value}'";
        if ($condition !== '') {
            $query .= " WHERE " . $condition;
        }
        $query .= ";";
        return parent::exec($query);
    }

    public function delete($table, $camp, $id) {
        $query = "DELETE FROM {$table} WHERE {$camp} = '{$id}';";
        return parent::exec($query);
    }

    public function assemble() {
        if (empty($this->camps) || empty($this->from)) {
            return false;
        }

        $this->query = "SELECT " . $this->camps . " FROM " . $this->from . ' ';

        //Juntamos JOINS
        foreach ($this->joins AS $key => $value) {
            if (count($value)) {
                for ($i = 0; $i <= count($value); $i++) {
                    $this->query .= $value[$i] . ' ';
                }
            }
        }

        //Agregamos condciones
        if (count($this->wheres['and']) || count($this->wheres['or'])) {
            $this->query .= 'WHERE ';

            $ands = 0;
            $ors = 0;

            foreach ($this->wheres AS $key => $value) {
                if (count($value)) {
                    switch ($key) {
                        case 'and':
                            $ands ++;

                            if ($ors > 0) {
                                $this->query .= ' AND ';
                            }

                            $this->query .= implode(' AND ', $value);
                            break;
                        case 'or':
                            $ors ++;

                            if ($ands > 0) {
                                $this->query .= ' OR ';
                            }

                            $this->query .= implode(' OR ', $value);
                            break;
                    }
                }
            }
        }

        //Agregamos groupBy
        if (strlen($this->groupBy)) {
            $this->query .= " GROUP BY {$this->groupBy}";
        }

        //Agregamos orderBy
        if (!empty($this->orderBy) && !empty($this->sortBy)) {
            $this->query .= " ORDER BY {$this->orderBy} {$this->sortBy}";
        }

        //Agregamos limit
        if (!empty($this->limit) && is_numeric($this -> limit) && intval($this->limit) > 0) {
            $this->query .= " LIMIT {$this->limit}";
        }

        $this->query .= ";";

        return $this->query;
    }

    public function clearQuery() {
        $this->query = '';
    }

}
