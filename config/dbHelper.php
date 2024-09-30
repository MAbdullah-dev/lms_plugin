<?php
class DBHelper {
    public static function bindParams($stmt, $params) {
        $types = '';
        $values = [];

        foreach ($params as $param) {
            $types .= $param['type'];
            $values[] = $param['value'];
        }

        $stmt->bind_param($types, ...$values);
    }
}
?>
