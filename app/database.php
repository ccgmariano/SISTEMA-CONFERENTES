<?php

class Database {

    private static $db;

    public static function connect() {
        if (!self::$db) {

            // Diretório de escrita permitido no Render
            $dir = '/var/data';

            // Criar diretório caso não exista
            if (!is_dir($dir)) {
                mkdir($dir, 0777, true);
            }

            $path = $dir . '/database.sqlite';

            self::$db = new PDO("sqlite:" . $path);
            self::$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
        return self::$db;
    }
}
