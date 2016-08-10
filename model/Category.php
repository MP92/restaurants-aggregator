<?php

/**
 * Created by PhpStorm.
 * User: Максим
 * Date: 10.08.2016
 * Time: 11:14
 */
class Category
{
    private $id;
    private $name;

    /**
     * Category constructor.
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
        return "Category{id=" . $this->id .
                ", name=" . $this->name .
                "}";
    }
}