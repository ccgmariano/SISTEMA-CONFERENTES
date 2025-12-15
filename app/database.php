<?php

class Database {

    private static $db;

    public static function connect() {

        if (!self::$db) {

            $dir  = '/var/data';
            $path = $dir . '/sistema_conferentes.sqlite';

            // Garantir que o diretório existe
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }

            // IMPORTANTE:
            // NÃO criar o arquivo manualmente
            // SQLite cria o arquivo sozinho

            self::$db = new PDO("sqlite:" . $path);
            self::$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }

        return self::$db;
    }
}
