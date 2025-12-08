<?php

class Database {

    private static $db;

    public static function connect() {

        if (!self::$db) {

            // O Render só permite escrita em /tmp
            $path = '/tmp/database.sqlite';

            // Se o arquivo não existir, cria vazio
            if (!file_exists($path)) {
                file_put_contents($path, '');
            }

            self::$db = new PDO('sqlite:' . $path);
            self::$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }

        return self::$db;
    }
}
