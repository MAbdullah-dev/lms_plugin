<?php
class DBHelper {
    public static function bindParams($stmt, $params) {
        foreach ($params as $param => $value) {
            $stmt->bindValue($param, $value);
        }
    }
}
