<?php

class Database {

    private static $db;

    public static function connect() {

        if (!self::$db) {

            // Local correto no Render (permite escrita)
            $path = '/var/data/sistema_conferentes.sqlite';


            // Se não existir, cria o arquivo
            if (!file_exists($path)) {
                file_put_contents($path, '');
            }

            self::$db = new PDO("sqlite:" . $path);
            self::$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }

        return self::$db;
    }
}
?>
