<?php

interface DatabaseInterface {
    public function select($camps = array('*'));
    public function from($table = array());
    public function innerJoin($table = array(), $condition = '');
    public function rightJoin($table = array(), $condition = '');
    public function leftJoin($table = array(), $condition = '');
    public function fullJoin($table = array(), $condition = '');
    public function addWhere($where);
    public function addOrWhere($orWhere);
    public function limit($limit);
    public function orderBy($camp, $order);
    public function groupBy($camps = array());
    
    public function insert($table, $camps, $values = array(), $multiple = false);
    public function update($table, $camp, $value, $condition = '');
    public function delete($table, $camp, $id);
    
    public function fetchAll();
    public function fetchRow();
}