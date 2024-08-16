<?php
define ('DB_SERVE', 'LOCALHOST');
define ('DB_USERNAME', 'ROOT');
define ('DB_PASSWORD', '');
define ('DB_NAME', 'TEST');

try {
    $PDO = new pdo("mysql:host=" . DB_SERVE . "db_name=" . DB_NAME, DB_USERNAME, DB_PASSWORD );
    $PDO ->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PODEXCEPTION $e) {
    die("ERROR: Não foi possivel conectar." . $e->getMessage());
}