<?php

class Database {

    private static $db;

    public static function connect() {
        if (!self::$db) {

            // SQLite deve ficar em /var/
            $path = '/var/database.sqlite';

            // cria o arquivo se não existir
            if (!file_exists($path)) {
                touch($path);
            }

            self::$db = new PDO("sqlite:" . $path);
            self::$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }

        return self::$db;
    }
}
