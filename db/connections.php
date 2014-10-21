<?php

class Connections {
    private static $connects;

    public static function register_connections() {
        foreach (Base::config()->databases AS $key => $value) {
            switch ($value->type) {
                case 'mysql':
                    self::$connects[$key] = new DatabaseMysql($value->host, $value->user, $value->password, $value->database);
                    break;
            }
        }
    }
    
    public static function get($connection_id) {
        if (key_exists($connection_id, self::$connects)) {
            return self::$connects[$connection_id];
        } else {
            return false;
        }
    }
}