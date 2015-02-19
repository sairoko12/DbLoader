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
     * You can add more cases for any database type please visit the oficial web site: http://mx2.php.net/manual/en/pdo.drivers.php for more info.
     */
    
    public static function register_connections() {
        foreach (Base::config()->databases AS $id => $property) {
            $connection = false;
            switch ($property->driver) {
                case 'mysql':
                    $connection = new \PDO("{$property->driver}:host={$property->host};port={$property->port};dbname={$property->database}", $property->user, $property->password);
                    break;
                case 'oci':
                    $connection = new \PDO("{$property->driver}:dbname={$property->database};host={$property->host};port={$property->port}",$property->user,$property->password);
                break;
                case 'ibm':
                    $connection = new \PDO("{$property->driver}:DRIVER={IBM DB2 ODBC DRIVER};DATABASE={$property->database};HOSTNAME={$property->host};PORT={$property->port};PROTOCOL=TCPIP;", $property->user, $property->password);
                    break;
                
                default:
                    $connection = new stdClass();
                    $connection->message = "Not found driver {$property->driver}";
                    break;
            }
            
            self::$connects[$id] = new Sql($connection);
        }
    }
    
    public static function getDefaultDataBase(){
        $default = 0;
        foreach (Base::config()->databases AS $id => $property) {
            if (isset($property->default)) {
                if (intval($property->default) === 1) {
                    $default = $id;
                    break;
                }
            }
        }
        
        return $default;
    }
    
    public static function get($connection_id = false) {
        if (FALSE === $connection_id) {
            return (object) self::$connects[self::getDefaultDataBase()];
        }
        
        if (key_exists($connection_id, self::$connects)) {
            return (object) self::$connects[$connection_id];
        } else {
            return false;
        }
    }
}
