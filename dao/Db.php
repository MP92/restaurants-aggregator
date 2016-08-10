<?php

/**
 * Singleton class for database connection
 */
class Db
{
    private static $instance = null;

    private $connection;

    private function __construct()
    {
        extract(require(ROOT . "config/dbParams.php"), EXTR_PREFIX_ALL, "db");
        try {
            $this->connection = new PDO("mysql:host=$db_host;dbname=$db_dbName;charset=utf8",
                $db_login, $db_password);

            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (\PDOException $e) {
            echo $e->getMessage();
        }
    }

    private function __clone()
    {
    }

    public static function getConnection()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance->connection;
    }
}
