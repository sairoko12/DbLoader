<?php

/**
 * The use this class is only for assemble easy queries for advanced options
 * please read more about the PDO in the next link: http://php.net/manual/es/class.pdo.php
 *
 */
class Sql {

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

    public function __construct(PDO $driver) {
        $this->setDb($driver);
        /*         * $this->db = $db;
          $this->driver = $this->db->getAttribute(PDO::ATTR_DRIVER_NAME);


         * Aqui se puede agregar nuevos cases para modificar los parametros que necesites para generar tus querys de acuerdo a la base de datos que estes utilizando.
         * Ejemplo: case 'oci': para oracle, case 'ibm': para DB2

          switch ($this->driver) {
          case 'mysql': default:
          $this->wheres = array('and' => array(), 'or' => array());
          $this->joins = array('inner' => array(), 'left' => array(), 'right' => array(), 'full' => array());
          break;
          } */
    }

    public function select($camps = array()) {
        if (is_array($camps) && !empty($camps)) {
            $this->camps = implode(',', $camps);
        } elseif (is_array($camps) && empty($camps)) {
            $this->camps = '*';
        } elseif ($camps !== '') {
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

    public function where($where, $wildcard = null) {
        if (is_null($wildcard)) {
            $this->wheres['and'][] = $where;
        } else {
            if (FALSE !== strpos($where, '?')) {
                $this->wheres['and'][] = str_replace("?", "'{$wildcard}'", $where);
            }
        }

        return $this;
    }

    public function orWhere($orWhere, $wildcard = null) {
        if (is_null($wildcard)) {
            $this->wheres['or'][] = $orWhere;
        } else {
            if (FALSE !== strpos($orWhere, '?')) {
                $this->wheres['or'][] = str_replace("?", "'{$wildcard}'", $orWhere);
            }
        }

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
        $query = "INSERT INTO {$table}(" . implode(',', array_keys($data)) . ") VALUES(" . implode(',', array_map(function($value) {
                            return (is_numeric($value)) ? "{$value}" : "'{$value}'";
                        }, $data)) . ");";
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
        $query = "UPDATE {$table} SET " . implode(',', $this->array_map_assoc(function($k, $v) {
                            return (is_numeric($v)) ? "{$k} = {$v}" : "{$k} = '{$v}'";
                        }, $data));

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
        $query = "DELETE FROM {$table} WHERE " . implode(' AND ', $this->array_map_assoc(function($k, $v) {
                            return (is_numeric($v)) ? "({$k} = {$v})" : "({$k} = '{$v}')";
                        }, $data)) . ";";

        try {
            return $this->db()->exec($query);
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
        if ($this->validJoins()) {
            foreach ($this->joins AS $key => $value) {
                if (count($value)) {
                    for ($i = 0; $i < count($value); $i++) {
                        $query .= $value[$i] . ' ';
                    }
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

    public function db() {
        return $this->db;
    }

    public function isReady() {
        if (empty($this->from)) {
            return false;
        }

        if ($this->assemble() === '') {
            return false;
        }

        return true;
    }

    public function setDb(PDO $driver) {
        $this->db = $driver;
        return $this;
    }

    public function quickQuery($from, $where = array(), $camps = array(), $extras = array()) {
        $setup = $this->select($camps)->from($from);

        if (is_array($where) && !empty($where)) {
            foreach ($where as $k => $w) {
                if (is_array($w)) {
                    switch ($k) {
                        case 'and':
                            foreach ($w as $n => $sw) {
                                if (!is_numeric($n) && is_string($n)) {
                                    $setup->where($n . ' = ?', $sw);
                                } else {
                                    $setup->where($sw);
                                }
                            }
                            break;
                        case 'or':
                            foreach ($w as $n => $sw) {
                                if (!is_numeric($n) && is_string($n)) {
                                    $setup->orWhere($n . ' = ?', $sw);
                                } else {
                                    $setup->orWhere($sw);
                                }
                            }
                            break;
                        default:
                            return false;
                            break;
                    }
                } else {
                    if (!is_numeric($k) && !is_int($k)) {
                        $setup->where($k . ' = ?', $w);
                    } else {
                        $setup->where($w);
                    }
                }
            }
        } elseif ($where !== '') {
            $setup->where($where);
        }

        return $this;
    }

    public function validJoins() {
        if (count($this->joins) >= 1) {
            $counter = 0;
            foreach ($this->joins AS $type => $join) {
                if (count($join) >= 1) {
                    $counter ++;
                }
            }

            return ($counter <= 0) ? false : true;
        }

        return false;
    }

}
