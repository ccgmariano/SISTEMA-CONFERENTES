<?php

class Auth {

    public static function login($usuario, $senha) {

        if ($usuario === USER && $senha === PASS) {
            $_SESSION['logged_in'] = true;
            return true;
        }

        return false;
    }

    public static function check() {
        return isset($_SESSION['logged_in']);
    }

    public static function logout() {
        session_destroy();
    }
}
