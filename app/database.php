<?php

class Database
{
    private static $db = null;

    public static function connect()
    {
        if (self::$db === null) {

            // Caminho do disco persistente (Render)
            $dir  = '/var/data/db';
            $path = $dir . '/sistema_conferentes.sqlite';

            // Garante que o subdiretório exista
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }

            // Conexão SQLite (o próprio SQLite cria o arquivo)
            self::$db = new PDO('sqlite:' . $path);
            self::$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            self::$db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        }

        return self::$db;
    }
}
