<?php
    function getJson() {
        return json_decode(file_get_contents('php://input'), true);
    }
    
    function getParam($key) {
        return isset($_GET[$key]) ? $_GET[$key] : "";
    }

    function getUrl() {
        return isset($_GET['url']) ? rtrim($_GET['url'], '/') : "";
    }

    function getUrlPaths() {
        $getUrl = getUrl();        
        return $getUrl !== "" ? explode('/', $getUrl) : "";
    }

    function getMethod() {        
        return $_SERVER['REQUEST_METHOD'];
    }

    function isGetOne() {
        $urlPaths = getUrlPaths();
        if(isset($urlPaths[2])) { //one
            return $urlPaths[2];
        }
        return false;
    }
