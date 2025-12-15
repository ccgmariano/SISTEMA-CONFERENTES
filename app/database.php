<?php

class Database {

    private static $db;

    public static function connect() {

        if (!self::$db) {

            $dir  = '/var/data';
            $path = $dir . '/sistema_conferentes.sqlite';

            // Garantir que o diretório existe
            if (!is_dir($dir)) {
                mkdir($dir, 0777, true);
            }

            // Garantir permissão de escrita
            if (!is_writable($dir)) {
                chmod($dir, 0777);
            }

            self::$db = new PDO("sqlite:" . $path);
            self::$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }

        return self::$db;
    }
}
