<?php

class Fetch {
    const MODE_OBJECT = 1;
    const MODE_ARRAY = 2;
    
    public static function all(Sql $query, $to = self::MODE_OBJECT) {
        $obj = $query->db()->query($query->assemble());

        if (is_object($obj)) {
            if (1 === $to) {
                $result = $obj->fetchAll(PDO::FETCH_OBJ);
            } else {
                // You ca add more case's ;)
                switch ($to) {
                    case 2:
                        $result = $obj->fetchAll(PDO::FETCH_ASSOC);
                        break;
                    default:
                        $result = $obj->fetchAll($to);
                        break;
                }
            }

            if (is_array($result)) {
                return $result;
            } else {
                return false;
            }
        }

        return false;
    }

    public static function row(Sql $query, $to = self::MODE_OBJECT) {
        $obj = $query->db()->query($query->assemble());

        if (is_object($obj)) {
            if (1 === $to) {
                $result = $obj->fetch(PDO::FETCH_OBJ);
            } else {
                // You can add more case's ;)
                switch ($to) {
                    case 2:
                        $result = $obj->fetch(PDO::FETCH_ASSOC);
                        break;
                    default: 
                        $result = $obj->fetch($to);
                        break;
                }
                return $result;
            }

            if (is_object($result) || is_array($result)) {
                return $result;
            } else {
                return false;
            }
        }

        return false;
    }

    public static function fromString($query, $db = false) {
        $db = Connections::get($db);

        $sth = $db->db()->query($query);
        return $sth->fetchAll(PDO::FETCH_ASSOC);
    }
}
