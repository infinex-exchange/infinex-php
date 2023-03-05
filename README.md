

# infinex-php
Official PHP wrapper for the Infinex APIs.

# Installation
`composer require infinex-exchange/infinex-php`

# Usage
Check examples/ folder for more usage examples.

## API
Blocking API over HTTP:
```php
<?php
require __DIR__ . '/vendor/autoload.php';
    
try {
    $infinex = new Infinex\API(
	    new Infinex\Transport\HTTP('https://api.infinex.cc')
	);
	
    var_dump(
        $infinex -> wallet -> getAssets()
    );

	$infinex -> login('api_key_here');
    
    var_dump(
        $infinex -> wallet -> getBalance('USDT')
    );
    
    var_dump(
        $infinex -> spot -> getOrderBook('BPX/USDT')
    );
}
    
catch(Infinex\Exceptions\ConnException $e) {
    echo "Connection error: " . $e->getMessage();
}
    
catch(Infinex\Exceptions\InfinexException $e) {
    echo "Error from exchange: " . $e->getMessage();
}    
?>
```
Non-blocking async API over HTTP:
```php
<?php
require __DIR__ . '/vendor/autoload.php';
    
$infinex = new Infinex\API(
	new Infinex\Transport\HTTP('https://api.infinex.cc'),
	true
);

$infinex -> login('api_key_here');
	
$infinex -> wallet -> getBalance('BTC') -> then(
	function($response) {
		var_dump($response);
	},
	function($e) {
		echo get_class($e).': '.$e->getMessage()."\n";
	}
);

?>
```
Blocking API over WebSockets:
```php
<?php
require __DIR__ . '/vendor/autoload.php';

use React\EventLoop\Loop;

$loop = Loop::get();
   
try {
    $infinex = new Infinex\API(
	    new Infinex\Transport\WebSocket($loop, 'wss://mux.infinex.cc')
	);
	
    var_dump(
        $infinex -> wallet -> getAssets()
    );

	$infinex -> login('api_key_here');
    
    var_dump(
        $infinex -> wallet -> getBalance('USDT')
    );
    
    var_dump(
        $infinex -> spot -> getOrderBook('BPX/USDT')
    );
}
    
catch(Infinex\Exceptions\ConnException $e) {
    echo "Connection error: " . $e->getMessage();
}
    
catch(Infinex\Exceptions\InfinexException $e) {
    echo "Error from exchange: " . $e->getMessage();
}

$loop -> run();
?>
```

Non-blocking async API over WebSockets:
```php
<?php
require __DIR__ . '/vendor/autoload.php';

use React\EventLoop\Loop;

$loop = Loop::get();
   
$infinex = new Infinex\API(
	new Infinex\Transport\WebSocket($loop, 'wss://mux.infinex.cc'),
	true
);

$infinex -> login('api_key_here');
	
$infinex -> wallet -> getBalance('BTC') -> then(
	function($response) {
		var_dump($response);
	},
	function($e) {
		echo get_class($e).': '.$e->getMessage()."\n";
	}
);

$loop -> run();

?>
```

## Streams
- Available soon