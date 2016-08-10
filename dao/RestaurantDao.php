<?php

/**
 * Class for retrieving and saving information about restaurants into database.
 */
class RestaurantDao
{
    const INSERT_SQL = "INSERT INTO restaurants(name, city_id, address)
                                  VALUES (:name, :cityId, :address)
                                  ON DUPLICATE KEY UPDATE id=LAST_INSERT_ID(id)";

    const SELECT_BY_ID_SQL = "SELECT r.id as id, r.name as name, c.id as cityId, c.name as city, r.address as address FROM restaurants as r
                                LEFT JOIN cities as c ON (r.city_id=c.id)
                                WHERE r.id=:id";

    private $pdoConnection;

    private $insertStmt;
    private $selectByIdStmt;

    private static $instance = null;

    /**
     * RestaurantDao constructor.
     */
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
     * Insert new restaurant into database.
     *
     * @param Restaurant $restaurant
     * @param int $cityId
     * 
     * @return Restaurant $restaurant containing PK id
     * 
     * @throws Exception
     */
    public function save($restaurant, $cityId)
    {
        $this->insertStmt->bindParam(":name", $restaurant->getName(), PDO::PARAM_STR);
        $this->insertStmt->bindParam(":cityId", $cityId, PDO::PARAM_INT);
        $this->insertStmt->bindParam(":address", $restaurant->getAddress(), PDO::PARAM_STR);
        $restaurant->setCityId($cityId);
        if (!$this->insertStmt->execute()) {
            throw new Exception("Could not save to db $restaurant");
        }
        $restaurant->setId($this->pdoConnection->lastInsertId());

        return $restaurant;
    }

    /**
     * @param int $id
     * @return Restaurant
     * @throws Exception
     */
    public function getById($id)
    {
        $this->selectByIdStmt->bindParam(":id", $id, PDO::PARAM_INT);
        if ($this->selectByIdStmt->execute() === false) {
            throw new Exception("Can't perform getById query in RestaurantDao for id=$id.");
        }
        return Restaurant::createFromData($this->selectByIdStmt->fetch(PDO::FETCH_ASSOC));
    }
}