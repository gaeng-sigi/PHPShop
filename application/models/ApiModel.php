<?php

namespace application\models;

use PDO;

class ApiModel extends Model
{
    public function getCategoryList()
    {
        $sql = "SELECT * FROM t_category";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function productInsert(&$param)
    {
        $sql = "INSERT INTO t_product
                SET product_name = :product_name,
                    product_price = :product_price,
                    delivery_price = :delivery_price,
                    add_delivery_price = :add_delivery_price,
                    tags = :tags,
                    outbound_days = :outbound_days,
                    seller_id = :seller_id,
                    category_id = :category_id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(":product_name", $param["product_name"]);
        $stmt->bindValue(":product_price", $param["product_price"]);
        $stmt->bindValue(":delivery_price", $param["delivery_price"]);
        $stmt->bindValue(":add_delivery_price", $param["add_delivery_price"]);
        $stmt->bindValue(":tags", $param["tags"]);
        $stmt->bindValue(":outbound_days", $param["outbound_days"]);
        $stmt->bindValue(":seller_id", $param["seller_id"]);
        $stmt->bindValue(":category_id", $param["category_id"]);
        $stmt->execute();
        
        return intval($this->pdo->lastInsertId());
    }
}
