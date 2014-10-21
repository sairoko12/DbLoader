<?php

class Base extends stdClass {
    protected static $config_in = array();
    protected static $config_out;
    protected static $file_config = "config.json";
    protected static $type_file_config = "json";

    public static function init() {
        $data = file_get_contents(self::$file_config);
        (array) self::$config_in = json_decode($data, 1);
    }
    
    public static function config($toArray = false) {
        self::init();
        
        if ($toArray) {
            return self::$config_in;
        }

        //Preparamos el objecto
        self::$config_out = new Base();
        //Registramos las conexiones a base de datos
        $dbs = new Databases(self::$config_in['databases']);
        self::$config_out->databases = $dbs->register();

        return self::$config_out;
    }
}
