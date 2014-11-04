<?php

/**
 * Class manager for all connections
 * @author Cristian Benavides <sairoko16@gmail.com>
 * @version 1.0
 * 
 */

class Connections {
    protected static $connects;
    
    /**
     * You can add more cases for any database type please see: http://mx2.php.net/manual/en/pdo.drivers.php for more info.
     */
    public static function register_connections() {
        foreach (Base::config()->databases AS $key => $value) {
            switch ($value->driver) {
                case 'mysql':
                    self::$connects[$key] = new Mysql(new PDO("{$value->driver}:host={$value->host};port={$value->port};dbname={$value->database}", $value->user, $value->password));
                    break;
                case 'oci':
                    self::$connects[$key] = new Oracle(new PDO("{$value->driver}:dbname={$value->database};host={$value->host};port={$value->port}",$value->user,$value->password));
                break;
                
                default:
                    self::$connects[$key] = new stdClass();
                    self::$connects[$key]-> message = "Not found driver {$value->driver}";
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