<?php

/**
 * Class for retrieving and saving information about dishes into database.
 */
class DishDao
{
    const INSERT_SQL = "INSERT IGNORE INTO dishes(restaurant_id, name, category_id, price) 
                            VALUES (:restaurantId, :name, :categoryId, :price)";

    const SELECT_ALL_SQL = "SELECT d.id as id, d.name as name, ctg.id as ctgId, ctg.name as category, d.price as price,
                                r.id as rId, r.name as rName, c.id as cityId, c.name as city, r.address as rAddress 
                                FROM dishes as d
                                LEFT JOIN dish_category as ctg ON (ctg.id=d.category_id)
                                INNER JOIN restaurants as r ON (r.id=d.restaurant_id)
                                LEFT JOIN cities as c ON (r.city_id=c.id)";

    const SELECT_BY_ID_SQL = self::SELECT_ALL_SQL . " WHERE d.id = :id";
    
    const DELETE_ALL_SQL = "DELETE FROM dishes";

    const DEFAULT_LIMIT = 1000;

    private $pdoConnection;

    private $insertStmt;
    private $selectByIdStmt;

    private static $instance = null;

    private function __construct()
    {
        $this->pdoConnection = Db::getConnection();

        $this->insertStmt = $this->pdoConnection->prepare(self::INSERT_SQL);
        $this->selectByIdStmt = $this->pdoConnection->prepare(self::SELECT_BY_ID_SQL);
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

    /**
     * @param Dish $dish
     * @param int $restaurantId
     * @param int $categoryId
     * @return Dish $dish containing PK id
     * @throws Exception
     */
    public function save($dish, $restaurantId, $categoryId)
    {
        $this->insertStmt->bindParam(":name", $dish->getName(), PDO::PARAM_STR);
        $this->insertStmt->bindParam(":price", $dish->getPrice(), PDO::PARAM_STR);
        $this->insertStmt->bindParam(":restaurantId", $restaurantId, PDO::PARAM_INT);
        $this->insertStmt->bindParam(":categoryId", $categoryId, PDO::PARAM_INT);
        
        $dish->setRestaurantId($restaurantId);
        $dish->setCategoryId($categoryId);
        
        if (!$this->insertStmt->execute()) {
            throw new Exception("Could not save to db $dish");
        }
        
        $dish->setId($this->pdoConnection->lastInsertId());
        return $dish;
    }

    public function getById($id)
    {
        $this->selectByIdStmt->bindParam(":id", $id, PDO::PARAM_INT);
        if ($this->selectByIdStmt->execute() === false) {
            throw new Exception("Can't perform getById query in DishDao for id=$id.");
        }
        return Dish::createFromData($this->selectByIdStmt->fetch(PDO::FETCH_ASSOC));
    }

    public function getListByName($name, $order = false)
    {
        return $this->getListByCriterion("d.name", $name, $order);
    }

    public function getListByCategory($category, $order = false)
    {
        return $this->getListByCriterion("ctg.name", $category, $order);
    }

    public function getListByCity($city, $order = false)
    {
        return $this->getListByCriterion("c.name", $city, $order);
    }


    private function getListByCriterion($criterionName, $criterionValue, $order)
    {
        $sql = $this->getSql($criterionName, $criterionValue, $order);
        if (($stmt = $this->pdoConnection->query($sql)) === false) {
            throw new Exception("Can't get dish list by $criterionName in class " . __CLASS__ . ".");
        }
        return $this->toDishList($stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    private function getSql($criterionName, $criterionValue, $order)
    {
        $criterionValue = $this->pdoConnection->quote("%" . $criterionValue . "%");
        $sql = self::SELECT_ALL_SQL . " WHERE $criterionName LIKE $criterionValue";
        if (($order == "price_asc") || ($order == "price_desc"))  {
            $sqlOrder = ($order == "price_desc") ? "DESC" : "ASC";
            $sql .= " ORDER BY LENGTH(SUBSTRING_INDEX(d.price, ' - ', 1)) $sqlOrder, d.price $sqlOrder";
        }
        $sql .= " LIMIT " . self::DEFAULT_LIMIT;
        return $sql;
    }

    /**
     * @param array $resultSet Array containing all of the remaining rows in the result set
     * @return array Array of {@link Dish}
     */
    private function toDishList($resultSet)
    {
        $dishList = [];
        foreach ($resultSet as $row) {
            $dishList[] = Dish::createFromData($row);
        }
        return $dishList;
    }
    
    public function deleteAll()
    {
        if (($stmt = $this->pdoConnection->query(self::DELETE_ALL_SQL)) === false) {
            throw new Exception("Can't perform " . __METHOD__ . ".");
        }
    }
}