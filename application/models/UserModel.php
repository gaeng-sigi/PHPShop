<?php

namespace application\models;

use PDO;

class UserModel extends Model {
    public function signup(&$param) {
        $sql = "INSERT INTO t_user (social_type, email, nickname, profile_img, thumb_img)
        VALUES(:social_type, :email, :nickname, :profile_img, :thumb_img)
        ON duplicate key update
        updated_at = now()";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(":social_type", $param["social_type"]);
        $stmt->bindValue(":email", $param["email"]);
        $stmt->bindValue(":nickname", $param["nickname"]);
        $stmt->bindValue(":profile_img", $param["profile_img"]);
        $stmt->bindValue(":thumb_img", $param["thumb_img"]);
        $stmt->execute();

        return intval($this->pdo->lastInsertId());
    }
}