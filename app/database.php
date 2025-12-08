<?php

class Database {

    private static $db;

    public static function connect() {

        if (!self::$db) {

            // Local seguro para escrita no Render
            $path = '/tmp/database.sqlite';

            // Se o arquivo não existe, criar
            if (!file_exists($path)) {
                touch($path);
                chmod($path, 0666);
            }

            self::$db = new PDO("sqlite:" . $path);
            self::$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }

        return self::$db;
    }
}
