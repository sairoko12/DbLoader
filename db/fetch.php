<?php

class Fetch {
    public static function all(Sql $query, $to = true) {
        $obj = $query->db()->query($query->assemble());

        if (is_object($obj)) {
            if (TRUE === $to) {
                $result = $obj->fetchAll(PDO::FETCH_OBJ);
            } elseif ($to !== '') {
                // You ca add more case's ;)
                switch ($to) {
                    case 'array': default:
                        $result = $obj->fetchAll(PDO::FETCH_ASSOC);
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

    public static function row(Sql $query, $to = true) {
        $obj = $query->db()->query($query->assemble());

        if (is_object($obj)) {
            if (TRUE === $to) {
                $result = $obj->fetch(PDO::FETCH_OBJ);
            } elseif ($to !== '') {
                // You can add more case's ;)
                switch ($to) {
                    case 'array': default:
                        $result = $obj->fetch(PDO::FETCH_ASSOC);
                        break;
                }
            }

            if (is_object($result)) {
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
