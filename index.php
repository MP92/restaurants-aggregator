<?php
define("ROOT", dirname(__FILE__) . DIRECTORY_SEPARATOR);

require_once "components/autoload.php";
require_once "components/utils.php";

$keyword = "";
$searchCriterion = "name";
$order = "false";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $keyword = filterParam($_POST["keyword"]);
    $searchCriterion = filterParam($_POST["searchCriterion"]);
    $order = filterParam($_POST["order"]);

    if (!empty($keyword) && !empty($searchCriterion)) {
        $dishDao = DishDao::getInstance();
        $methodName = "getListBy" . ucfirst($searchCriterion);
        $dishList = call_user_func_array([$dishDao, $methodName], [$keyword, $order != "false" ? $order : false]);
    }
}
?>
<!DOCTYPE html>
<html lang="ru-en">
<head>
    <meta charset="UTF-8">
    <title>Hi</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="clearfix">
    <div class="div-left div-relative">
        <form method="post">
            <fieldset>
                <legend>Форма поиска</legend>
                <h3>Ключевое слово</h3>
                <p class="form-section">
                    <input type="text" name="keyword" placeholder="Key word" value="<?= $keyword ?>"/>
                </p>
                <h3>Критерий поиска</h3>
                <p class="form-section">
                    <label>
                        <input name="searchCriterion" type="radio" value="name" <?= $searchCriterion == "name" ? "checked" : "" ?>/>
                        По имени
                    </label>
                    <span><b>|</b></span>
                    <label>
                        <input name="searchCriterion" type="radio" value="category" <?= $searchCriterion == "category" ? "checked" : "" ?>/>
                        По категории
                    </label>
                    <span><b>|</b></span>
                    <label>
                        <input name="searchCriterion" type="radio" value="city" <?= $searchCriterion == "city" ? "checked" : "" ?>/>
                        По городу
                    </label>
                </p>
                <h3>Сортировка</h3>
                <p class="form-section">
                    <label>
                        <input name="order" type="radio" value="price_asc" <?= $order == "price_asc" ? "checked" : "" ?>>
                        Цена(по возрастанию)
                    </label>
                    <span><b>|</b></span>
                    <label>
                        <input name="order" type="radio" value="price_desc" <?= $order == "price_desc" ? "checked" : "" ?>>
                        Цена(по убыванию)
                    </label>
                    <span><b>|</b></span>
                    <label>
                        <input name="order" type="radio" value="false" <?= $order == "false" ? "checked" : "" ?>>
                        Без сортировки
                    </label>
                </p>
                <p><input type="submit" value="Поиск"></p>
            </fieldset>
            <a class="btn" href="deleteAll.php?delete" onclick="return confirm('Вы уверены?')">Удалить все</a>
        </form>
    </div>
    <div class="div-left">
        <form action="getInfo.php" method="post">
            <fieldset>
                <legend>Форма для сбора информации</legend>
                <p><input type="text" name="url" placeholder="URL"></p>
                <p><input type="submit" value="run"></p>
            </fieldset>
        </form>
    </div>
</div>

<?php if (isset($dishList)): ?>
    <p>Ключевое слово: <b><?= $keyword ?></b></p>
    <p>Критерий поиска: <b><?= $searchCriterion ?></b></p>
    <p><b><?php
        echo ($order != "false" ?
                ("Сортировка по цене по " . ($order == "price_asc" ? "возрастанию" : "убыванию"))
                                :
                "Без сортировки") ?></b></p>
<h1>Список блюд(первые 1000)</h1>
<table>
    <tr>
        <th>Dish ID</th>
        <th>Dish name</th>
        <th>Dish category ID</th>
        <th>Dish category name</th>
        <th>Dish price</th>
        <th class="delim"></th>
        <th>Restaurant ID</th>
        <th>Restaurant name</th>
        <th>Restaurant city ID</th>
        <th>Restaurant city name</th>
        <th>Restaurant address</th>
    </tr>
    <?php foreach($dishList as $dishItem): ?>
    <tr>
        <td><?= $dishItem->getId() ?></td>
        <td><?= $dishItem->getName() ?></td>
        <td><?= $dishItem->getCategory()->getId() ?></td>
        <td><?= $dishItem->getCategory()->getName() ?></td>
        <td><?= $dishItem->getPrice()  ?></td>
        <td class="delim"></td>
        <td><?= $dishItem->getRestaurant()->getId() ?></td>
        <td><?= $dishItem->getRestaurant()->getName() ?></td>
        <td><?= $dishItem->getRestaurant()->getCity()->getId() ?></td>
        <td><?= $dishItem->getRestaurant()->getCity()->getName() ?></td>
        <td><?= $dishItem->getRestaurant()->getAddress() ?></td>
    </tr>
    <? endforeach; ?>
</table>
<?php endif; ?>
</body>
</html>