<?php

class Base extends stdClass {
    protected static $config_in = array();
    protected static $config_out;
    
    public static function config($toArray = false) {
        $data = file_get_contents(FILE_CONFIG, FILE_USE_INCLUDE_PATH);
        (array) self::$config_in = json_decode($data, 1);
        
        if ($toArray) {
            return self::$config_in;
        }

        self::$config_out = new self();
        $dbs = new Databases(self::$config_in['databases']);
        self::$config_out->databases = $dbs->register();

        return self::$config_out;
    }
}
