<?php

namespace application\models;

use PDO;

class ApiModel extends Model {
    public function getCategoryList() {
        $sql = "SELECT * FROM t_category";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }
}