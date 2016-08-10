<?php
define("ROOT", dirname(__FILE__) . DIRECTORY_SEPARATOR);

require_once "components/autoload.php";
require_once "Aggregator.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $url = trim(strip_tags($_POST["url"]));
    $aggregator = new Aggregator();
    $aggregator->run($url);
}
?>
<p><a href="index.php">Главная страница</a></p>
