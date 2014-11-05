<?php

class Mysql extends BuilderSql {
    public function __construct(PDO $db_object){
        parent::__construct($db_object);
    }
}
