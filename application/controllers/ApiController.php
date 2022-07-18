<?php

namespace application\controllers;

class ApiController extends Controller {
    public function categoryList() {
            return $this->model->getCategoryList();
    }
}