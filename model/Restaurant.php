<?php

/**
 * Class representing restaurant information
 */
class Restaurant
{
    private $id;
    private $name;
    private $address;
    private $cityId;
    
    private $city;

    /**
     * Restaurant constructor.
     * @param string $name
     * @param string $address
     * @param int $cityId
     * @param string $cityName
     * @param int $id
     */
    public function __construct($name, $address, $cityId = null, $cityName = null, $id = null)
    {
        $this->name = $name;
        $this->address = $address;
        $this->cityId = $cityId;
        $this->id = $id;
        $this->city = $cityName ? new City($cityId, $cityName) : null;
    }


    /**
     * Convenient method for restaurant instantiation from db result set row
     * 
     * @param array $data Associative array containing data about restaurant and city
     * @return Restaurant
     */
    public static function createFromData($data)
    {
        extract($data, EXTR_PREFIX_ALL, "arg");
        $restaurant = new Restaurant($arg_name, $arg_address, $arg_cityId);
        $restaurant->setId($arg_id);
        $restaurant->setCity(new City($arg_cityId, $arg_city));
        return $restaurant;
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
    public function getCityId()
    {
        return $this->cityId;
    }

    /**
     * @param int $cityId
     */
    public function setCityId($cityId)
    {
        $this->cityId = $cityId;
    }

    /**
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @param string $city
     */
    public function setCity($city)
    {
        $this->city = $city;
    }

    /**
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param string $address
     */
    public function setAddress($address)
    {
        $this->address = $address;
    }

    public function __toString()
    {
        return "Restaurant{id=" . $this->id .
                ", name=" . $this->name .
                ", city=" . $this->city .
                ", address=" . $this->address .
                "}";
    }
}