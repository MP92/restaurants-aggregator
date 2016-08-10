<?php

/**
 * Class for saving dish category information into database.
 */
class CategoryDao
{
    const CATEGORY_SQL = "INSERT INTO dish_category(name) VALUES (:name)
                                  ON DUPLICATE KEY UPDATE id=LAST_INSERT_ID(id)";

    private $pdoConnection;

    private $categoryStmt;
    
    private static $instance = null;
    
    private function __construct()
    {
        $this->pdoConnection = Db::getConnection();

        $this->categoryStmt = $this->pdoConnection->prepare(self::CATEGORY_SQL);
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
}