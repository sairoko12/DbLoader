<?php

class Databases extends ArrayIterator {
    private $databases;

    public function __construct($databases) {
        foreach ($databases AS $id => $propertis) {
            $this->databases[$id] = new stdClass();
            
            foreach ($propertis AS $property => $value) {
                if ($property === 'extends') {
                    if (key_exists($value, $this -> databases)) {
                        foreach ($this->databases[$value] AS $property_extend => $value_extend) {
                            if ($property_extend !== "default") {
                                $this->databases[$id]->$property_extend = $value_extend;
                            }
                        }
                        
                        if ($property !== 'extends' && $property !== "default") {
                            $this->databases[$id]->$property = $value;
                        }
                    }
                } else {
                    $this->databases[$id]->$property = $value;
                }
            }
        }
    }
    
    public function __destruct() {
    
    }

    public function register() {
        return (object) $this->databases;
    }
}
