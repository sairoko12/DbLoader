<?php

/**
 * Class manager for all connections
 * @author Cristian Benavides <sairoko16@gmail.com>
 * @version 1.0
 * 
 */

class Connections {
    protected static $connects = array();
    
    /**
     * You can add more cases for any database type please see: http://mx2.php.net/manual/en/pdo.drivers.php for more info.
     */
    public static function register_connections() {
        foreach (Base::config()->databases AS $id => $property) {
            switch ($property->driver) {
                case 'mysql':
                    self::$connects[$id] = new Mysql(new PDO("{$property->driver}:host={$property->host};port={$property->port};dbname={$property->database}", $property->user, $property->password));
                    break;
                case 'oci':
                    self::$connects[$id] = new Oracle(new PDO("{$property->driver}:dbname={$property->database};host={$property->host};port={$property->port}",$property->user,$property->password));
                break;
                
                default:
                    self::$connects[$id] = new stdClass();
                    self::$connects[$id]-> message = "Not found driver {$property->driver}";
                    break;
            }
        }
    }
    
    public static function get($connection_id) {
        if (key_exists($connection_id, self::$connects)) {
            return (object) self::$connects[$connection_id];
        } else {
            return false;
        }
    }
}
