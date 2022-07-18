<?php
    function getLoginUser() {
        return isset($_SESSION[_LOGINUSER]) ? $_SESSION[_LOGINUSER] : null;
    }

    function getIuser() {
        return getLoginUser() === null ? 0 : getLoginUser() -> ignore_user_abort;
    }

