<?php
require ROOT . "libs/phpQuery/phpQuery.php";
require ROOT . "libs/RollingCurl/RollingCurl.php";

/**
 * Class which collects data about restaurants including menu from website 'allmenus.com'
 */
class Aggregator
{
    const DEFAULT_CONNECTIONS_COUNT = 40;

    private $baseUrl;

    private $cityId;

    private $rc;

    /**
     * Aggregator constructor.
     * @param int $connectionsCount
     */
    public function __construct($connectionsCount = self::DEFAULT_CONNECTIONS_COUNT)
    {
        $rc = new RollingCurl(array($this, "processPage"));
        $rc->window_size = $connectionsCount;
        $rc->options = array(
            CURLOPT_USERAGENT => "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13",
        );
        $this->rc = $rc;
    }

    /**
     * Callback for RollingCurl.
     * Fetches information about restaurant and saves it into database.
     *
     * @param $response
     * @param $info
     */
    public function processPage($response, $info)
    {
        $doc = phpQuery::newDocument($response);
        $restaurantName = trim($doc->find("h1[itemprop='name']")->text());
        $restaurantAddress = trim(preg_replace('/\s+/', ' ', $doc->find("span[itemprop='address']")->text()));
        $restaurant = new Restaurant($restaurantName, $restaurantAddress);
        $restaurantMenu = $this->fetchMenu($doc["#menu > .category"]);

        $this->saveRestaurantInfoToDB($restaurant, $restaurantMenu);
    }

    /**
     * @param Restaurant $restaurant
     * @param array $menu Associative array (category => {@link Dish})
     * @throws Exception
     */
    private function saveRestaurantInfoToDB($restaurant, $menu)
    {
        $restaurant = RestaurantDao::getInstance()->save($restaurant, $this->cityId);
        foreach ($menu as $category => $dishArr) {
            $categoryId = CategoryDao::getInstance()->save($category);
            foreach ($dishArr as $dish) {
                DishDao::getInstance()->save($dish, $restaurant->getId(), $categoryId);
            }
        }
    }

    private function fetchMenu($menuDOMElement)
    {
        $menuInfo = [];
        foreach ($menuDOMElement as $menuItem) {
            $item = pq($menuItem);

            $category = strtolower(trim($item->find(".category_head > h3")->text()));

            $dishes = $item->find(".menu_item");
            foreach ($dishes as $dish) {
                $dishInfo = $dish->getElementsByTagName("span");
                $name = trim($dishInfo[0]->nodeValue);
                $price = trim($dishInfo[1]->nodeValue);
                $menuInfo[$category][] = new Dish($name, $price);
            }
        }
        return $menuInfo;
    }

    /**
     * Main method.
     * Collects information about restaurants and stores it into the database
     *
     * @param string $url Url like 'http://www.allmenus.com/ga/atlanta/-/' to a page contains a list of restaurants
     */
    public function run($url)
    {
        $start = microtime(true);

        $this->baseUrl = parse_url($url, PHP_URL_HOST);

        $doc = $this->getPhpQueryDoc($url);

        $cityName = trim($doc["#crumb_city > span"]->text());
        $this->cityId = CityDao::getInstance()->save($cityName);

        $urls = $this->getRestaurantUrls($doc);

        $this->requestAll($urls);

        echo "<h2>Runtime: " . (microtime(true) - $start) . "ms</h2><hr>";
    }

    /**
     * @param phpQueryObject $phpQueryDoc
     * @return array
     */
    private function getRestaurantUrls($phpQueryDoc)
    {
        $urls = [];
        $links = $phpQueryDoc->find("#restaurant_list .restaurant_name > a");
        foreach ($links as $link) {
            $urls[] = $this->relToAbs(pq($link)->attr("href"));
        }
        return $urls;
    }

    /**
     * Initializes curl requests for all urls
     *
     * @param array $urls
     */
    private function requestAll($urls)
    {
        foreach ($urls as $url) {
            $this->rc->get($url);
        }
        $this->rc->execute();
    }

    private function relToAbs($relUrl)
    {
        return "http://" . $this->baseUrl . $relUrl;
    }

    /**
     * @param $url
     * @return phpQueryObject
     * @throws Exception
     */
    private function getPhpQueryDoc($url)
    {
        if (!($html = file_get_contents($url))) {
            throw new Exception("Failed to get html by url '$url'");
        }
        return phpQuery::newDocument($html);
    }
}
