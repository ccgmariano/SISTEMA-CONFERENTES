<?php

class Database {

    private static $db;

    public static function connect() {
        if (!self::$db) {

            // Caminho seguro para escrita no Render
            $path = '/tmp/database.sqlite';

            // Se o arquivo não existir, cria
            if (!file_exists($path)) {
                touch($path); // cria arquivo vazio
            }

            self::$db = new PDO("sqlite:" . $path);
            self::$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }

        return self::$db;
    }
}
