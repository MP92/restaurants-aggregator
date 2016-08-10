<?php

/**
 * Class for saving restaurant city information into database.
 */
class CityDao
{
    const DELETE_ALL_SQL = "DELETE FROM cities";
    
    private $pdoConnection;
    
    private static $instance = null;
    
    private function __construct()
    {
        $this->pdoConnection = Db::getConnection();
    }

    private function __clone()
    {
    }
    
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function save($name)
    {
        $name = $this->pdoConnection->quote($name);
        $sql = "INSERT INTO cities(name) VALUES ($name)
                  ON DUPLICATE KEY UPDATE id=LAST_INSERT_ID(id)";
        if ($this->pdoConnection->exec($sql) === false) {
            throw new Exception("CityDao->save(): name=$name");
        }
        
        return $this->pdoConnection->lastInsertId();
    }

    public function deleteAll()
    {
        if (($stmt = $this->pdoConnection->query(self::DELETE_ALL_SQL)) === false) {
            throw new Exception("Can't perform " . __METHOD__ . ".");
        }
    }
}