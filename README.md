# restaurants-aggregator

### Page with interface for searching dishes and collecting information from URL
```
index.php
```


### Main class. Information collector
```
Aggregator.php
```

#### Usage

```php
//create new instance with default connections count(40)
$aggregator = new Aggregator(); 

//create new instance with specified connections count
$aggregator = new Aggregator(10); 

$aggregator->run($url);
```