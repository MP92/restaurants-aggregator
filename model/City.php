<?php

/**
 * Created by PhpStorm.
 * User: Максим
 * Date: 10.08.2016
 * Time: 11:42
 */
class City
{
    private $id;
    private $name;

    /**
     * City constructor.
     * @param int $id
     * @param string $name
     */
    public function __construct($id, $name)
    {
        $this->id = $id;
        $this->name = $name;
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

    public function __toString()
    {
        return "City{id=" . $this->id .
                ", name=" . $this->name .
                "}";
    }
}