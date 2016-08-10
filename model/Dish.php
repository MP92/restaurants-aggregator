<?php

/**
 * Class representing dish information including owning restaurant
 */
class Dish
{
    private $id;
    private $name;
    private $categoryId;
    private $price;
    private $restaurantId;
    
    private $category;
    private $restaurant;

    /**
     * Dish constructor.
     * @param string $name
     * @param string $price
     * @param int $categoryId
     * @param int $restaurantId
     */
    public function __construct($name, $price, $categoryId = null, $restaurantId = null)
    {
        $this->name = $name;
        $this->price = $price;
        $this->categoryId = $categoryId;
        $this->restaurantId = $restaurantId;
        
        $this->id = null;
        $this->category = null;
        $this->restaurant = null;
    }


    /**
     * Convenient method for dish instantiation from db result set row
     * 
     * @param array $data Associative array containing data about dish, category, restaurant and city
     * @return Dish
     */
    public static function createFromData($data)
    {
        extract($data, EXTR_PREFIX_ALL, "arg");
        $dish = new Dish($arg_name, $arg_price, $arg_ctgId, $arg_rId);
        $dish->setId($arg_id);
        $dish->setCategory(new Category($arg_ctgId, $arg_category));
        $dish->setRestaurant(new Restaurant($arg_rName, $arg_rAddress, $arg_cityId, $arg_city, $arg_rId));
        return $dish;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return int
     */
    public function getCategoryId()
    {
        return $this->categoryId;
    }

    /**
     * @param int $categoryId
     */
    public function setCategoryId($categoryId)
    {
        $this->categoryId = $categoryId;
    }

    /**
     * @return int
     */
    public function getRestaurantId()
    {
        return $this->restaurantId;
    }

    /**
     * @param int $restaurantId
     */
    public function setRestaurantId($restaurantId)
    {
        $this->restaurantId = $restaurantId;
    }

    /**
     * @return Restaurant
     */
    public function getRestaurant()
    {
        return $this->restaurant;
    }

    /**
     * @param Restaurant $restaurant
     */
    public function setRestaurant($restaurant)
    {
        $this->restaurant = $restaurant;
    }

    /**
     * @return string
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @param string $category
     */
    public function setCategory($category)
    {
        $this->category = $category;
    }

    /**
     * @return string
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @param string $price
     */
    public function setPrice($price)
    {
        $this->price = $price;
    }

    public function __toString()
    {
        return "Dish{id=" . $this->id .
                ", name=" . $this->name .
                ", city=" . $this->restaurant .
                ", category=" . $this->category .
                ", price=" . $this->price .
                "}";
    }
}