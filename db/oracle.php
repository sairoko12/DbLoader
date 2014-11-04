<?php

class Oracle extends BuilderSql {
    protected $pdo;
    
    public function __construct(PDO $pdo_object){
        $this->pdo = $pdo_object;
        parent::__construct($this->pdo->getAttribute(PDO::ATTR_DRIVER_NAME));
    }
    
    public function db(){
        return $this->pdo;
    }
}