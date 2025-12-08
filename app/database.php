<?php

class Database {

    private static $db;

    public static function connect() {
        if (!self::$db) {

            // CAMINHO CORRETO PARA O SQLITE (na pasta app/)
            $folder = __DIR__;
            $path = $folder . '/database.sqlite';

            // criar arquivo se não existir
            if (!file_exists($path)) {
                // cria arquivo vazio
                file_put_contents($path, '');
            }

            // abrir banco
            self::$db = new PDO("sqlite:" . $path);
            self::$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
        return self::$db;
    }
}
