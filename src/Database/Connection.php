<?php

class Connection
{
    public static function getConnection(): PDO
    {
        $host = 'localhost';
        $dbname = 'gerenciador_financeiro';
        $user = 'root';
        $password = '';

        $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8";

        return new PDO($dsn, $user, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
    }
}