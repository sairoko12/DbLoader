<?php

class Fetch {
    public static function all(Sql $query, $to = true) {
        if ($query->isReady()) {
            $sth = $query->db()->prepare($query->assemble());
            $sth->execute();
            
            if (TRUE === $to) {
                $result = $sth->fetchAll(PDO::FETCH_OBJ);
            } elseif($to !== '') {
                // You ca add more case's ;)
                switch ($to) {
                    case 'array': default: 
                        $result = $sth->fetchAll(PDO::FETCH_ASSOC);
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
        if (!$query->isReady()){
            return false;
        }
        
        $obj = $query->db()->query($query->assemble());

        if (is_object($obj)) {
            if (TRUE === $to) {
                $result = $obj->fetch(PDO::FETCH_OBJ);
            } elseif($to !== '') {
                // You ca add more case's ;)
                switch ($to) {
                    case 'array': default: 
                        $result = $obj->fetch();
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

}
