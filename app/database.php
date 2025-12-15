<?php

class Database
{
    private static $db = null;

    public static function connect()
    {
        if (self::$db === null) {

            $path = '/var/data/db/sistema_conferentes.sqlite';

            self::$db = new PDO('sqlite:' . $path);
            self::$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            self::$db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        }

        return self::$db;
    }
}
