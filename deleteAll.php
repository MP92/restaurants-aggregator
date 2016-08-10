<?php
define("ROOT", dirname(__FILE__) . DIRECTORY_SEPARATOR);

require_once "components/autoload.php";

if (isset($_GET["delete"])) {
    DishDao::getInstance()->deleteAll();
    RestaurantDao::getInstance()->deleteAll();
    CityDao::getInstance()->deleteAll();
    CategoryDao::getInstance()->deleteAll();

    echo "<h1>Все данные удалены.</h1>";
}
?>

<p><a href="index.php">Главная страница</a></p>
