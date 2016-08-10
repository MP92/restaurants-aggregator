<?php

/**
 * Class for saving dish category information into database.
 */
class CategoryDao
{
    const INSERT_SQL = "INSERT INTO dish_category(name) VALUES (:name)
                                  ON DUPLICATE KEY UPDATE id=LAST_INSERT_ID(id)";

    const DELETE_ALL_SQL = "DELETE FROM dish_category";

    private $pdoConnection;

    private $categoryStmt;
    
    private static $instance = null;
    
    private function __construct()
    {
        $this->pdoConnection = Db::getConnection();

        $this->categoryStmt = $this->pdoConnection->prepare(self::INSERT_SQL);
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
        $this->categoryStmt->bindParam(":name", $name, PDO::PARAM_STR);
        if (!$this->categoryStmt->execute()) {
            throw new Exception("CategoryDao->save(): name=$name");
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