<?php
    define('DB_USER', 'root');
    define('DB_PASSWORD', '');
    define('DB_HOST', 'localhost');
    define('DB_NAME', 'cia');
    define('CHARSET', 'utf8mb4');

    try {
        if(defined("INITIALIZING_DATABASE"))
            $data_source_name = "mysql:host=".DB_HOST.";charset=".CHARSET;
        else
            $data_source_name = "mysql:host=".DB_HOST.";dbname=".DB_NAME.";charset=".CHARSET;
        $pdo = new PDO($data_source_name, DB_USER, DB_PASSWORD);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        echo "Connnection failed: ".$e->getMessage();
    }




