<?php

class Database {

    private static $db;

    public static function connect() {
        if (!self::$db) {
            $path = __DIR__ . '/../database.sqlite';
            self::$db = new PDO("sqlite:" . $path);
            self::$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
        return self::$db;
    }
}
