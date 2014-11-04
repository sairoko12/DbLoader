<?php

/**
 * The use this class is only for assemble easy queries for advanced options
 * please read more about the PDO in the next link: http://php.net/manual/es/class.pdo.php
 *
 */

class BuilderSql {
    private $type_database;
    private $camps;
    private $from;
    private $wheres;
    private $limit;
    private $orderBy;
    private $sortBy;
    private $groupBy;
    private $joins;
    
    public function __construct($driver) {
        $this->type_database = $driver;
        //If your database required special query add more case
        switch ($this->type_database) {
            default:
                $this->wheres = array('and' => array(), 'or' => array());
                $this->joins = array('inner' => array(), 'left' => array(), 'right' => array(), 'full' => array());
                break;
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

    public function insert($table, $data = array()) {
        $query = "INSERT INTO {$table} (" . implode(',', array_keys($data)) . ") VALUES ";

        foreach ($data AS $camp => $value) {
            if (is_array($value)) {
                $query .= "(" . implode(',', $value) . ")";
            } else {
                $query .= "{$value}";
            }
        }

        /* if ($multiple){
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

          $query = "INSERT INTO {$table} (" . implode(',',$camps) . ") VALUES (" . implode(',', (($multiple) ? $records : $values)) . ");"; */
        //$result = parent::prepare($query);
        //$result ->execute(array($table, implode(',',$camps)));
        return $query; //parent::lastInsertId();
    }

    public function array_map_assoc($callback, $array) {
        $r = array();

        foreach ($array as $key => $value) {
            $r[$key] = $callback($key, $value);
        }

        return $r;
    }

    public function update($table, $data, $condition = '') {
        $query = "UPDATE {$table} SET ";

        $query .= implode(',', $this->array_map_assoc(function($k, $v) {
                    return "{$k} = '{$v}'";
                }, $data));

        if ($condition !== '') {
            $query .= " WHERE " . $condition;
        }

        $query .= ";";
        return $query; //parent::exec($query);
    }

    public function delete($table, $camp, $id) {
        $query = "DELETE FROM {$table} WHERE {$camp} = '{$id}';";
        return parent::exec($query);
    }

    public function assemble() {
        if (empty($this->camps) || $this->from === false) {
            $query = false;
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
}
