<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/Project_Be1/Booking-movies/config/database.php';

class Database
{
    public static $connection = NULL;
    public function __construct()
    {
        if (self::$connection == NULL) {
            self::$connection = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
            self::$connection->set_charset('utf8mb4');
        }
    }

    public function select($sql)
    {
        $sql->execute();
        return $sql->get_result()->fetch_all(MYSQLI_ASSOC);
    }
} 