<?php

namespace application\controllers;

use Exception;

class ApiController extends Controller {
    public function categoryList() {
        return $this->model->getCategoryList();
    }

    public function productInsert() {
        $json = getJson();
        print_r($json);
        return [_RESULT => $this->model->productInsert($json)];
    }

    public function productList() {
        return $this->model->productList();
    }

    public function productList2() {
        return $this->model->productList2();
    }

    public function productDetail() {
        $urlPaths = getUrlPaths();
        if(!isset($urlPaths[2])) {
            exit();
        }
        $param = [
            'product_id' => intval($urlPaths[2])
        ];

        return $this->model->productDetail($param);
    }

    public function upload() {
        $urlPaths = getUrlPaths();
        if (!isset($urlPaths[2]) || !isset($urlPaths[3])) {
            exit();
        }
        $productId = intval($urlPaths[2]);
        $type = intval($urlPaths[3]);
        $json = getJson();
        $image_parts = explode(";base64,", $json["image"]);
        $image_type_aux = explode("image/", $image_parts[0]);
        $image_type = $image_type_aux[1];
        $image_base64 = base64_decode($image_parts[1]);
        $dirPath = _IMG_PATH . "/" . $productId . "/" . $type;
        $file_Nm = uniqid() . "." . $image_type;
        $filePath = $dirPath . "/" . $file_Nm;
        if (!is_dir($dirPath)) {
            mkdir($dirPath, 0777, true);
        }
        //$file = _IMG_PATH . "/" . $productId . "/" . $type . "/" . uniqid() . "." . $image_type;
        //$file = "static/" . uniqid() . "." . $image_type;
        $result = file_put_contents($filePath, $image_base64);

        // 이미지 db로~
        if($result) {
            $param = [
                'product_id' => $productId,
                'type' => $type,
                'path' => $file_Nm
            ];
        }

        return $this->model->productImageInsert($param);
    }

    public function productImageList() {
        $urlPaths = getUrlPaths();
        if(!isset($urlPaths[2])) {
            exit();
        }
        $productId = intval($urlPaths[2]);

        $param = [
            'product_id' => $productId
        ];

        return $this->model->productImageList($param);
    }

    public function productImageDelete() {
        $urlPaths = getUrlPaths();
        if (count($urlPaths) !== 6) {
            exit();
        }

        $result = 0;
        switch(getMethod()) {
            case _DELETE:
                $product_image_id = intval($urlPaths[2]);
                $product_id = intval($urlPaths[3]);
                $type = intval($urlPaths[4]);
                $path = $urlPaths[5];

                // 이미지 삭제
                $imgPath = _IMG_PATH . "/" . $product_id . "/" . $type . "/" . $path;

                if(unlink($imgPath)) {
                    // sql 삭제.
                    $param = ["product_image_id" => $product_image_id];
                    $result = $this->model->productImageDelete($param);
                };

                break;
        }
        return [_RESULT => $result];
    }

    public function deleteProduct() {
        $urlPaths = getUrlPaths();
        if(count($urlPaths) !== 3) {
            exit();
        }
        $imgPath = _IMG_PATH . "/" . $urlPaths[2];
        if (is_dir($imgPath)) {
            rmdirAll($imgPath);
        }


        $productId = intval($urlPaths[2]);

        try {
            $param = [
                "product_id" => $productId
            ];
            $this->model->beginTransaction();
            $this->model->productImageDelete($param);
            $result = $this->model->productDelete($param);
            if ($result === 1) {
                $this->model->commit();
            } else {
                $this->model->rollback();
            }
        } catch (Exception $e) {
            $this->model->rollback();
        }

        return [_RESULT => 1];
    }
}