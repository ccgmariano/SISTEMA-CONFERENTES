<?php

class Database {

    private static $db;

    public static function connect() {
        if (!self::$db) {

            // Caminho correto para /app/database.sqlite
            $path = __DIR__ . '/database.sqlite';

            // Cria o arquivo se não existir
            if (!file_exists($path)) {
                touch($path);
            }

            self::$db = new PDO("sqlite:" . $path);
            self::$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }

        return self::$db;
    }
}
