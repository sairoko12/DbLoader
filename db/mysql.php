<?php

class Mysql extends BuilderSql {
    protected $pdo;
    
    public function __construct(PDO $pdo_object){
        $this->pdo = $pdo_object;
        parent::__construct($this->pdo->getAttribute(PDO::ATTR_DRIVER_NAME));
    }
    
    public function fetchAll() {
        $query = parent::assemble();
        if ($query) {
            $sth = $this->pdo->prepare($query);
            $sth -> execute();
            
            $result = $sth -> fetchAll(PDO::FETCH_OBJ);
            
            return (is_array($result)) ? $result : false;
        }

        return false;
    }

    public function fetchRow() {
        $query = parent::assemble();
        
        if ($query) {
            $obj = $this->pdo->query($query);
            
            if (is_object($obj)) {
                $result = $obj->fetch(PDO::FETCH_OBJ);
                return (is_object($result)) ? $result : false;
            }
            
            return false;
        }

        return false;
    }
    
    public function db(){
        return $this->pdo;
    }
}