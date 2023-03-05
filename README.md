

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
To use Infinex streams, you need to create a ReactPHP EventLoop, initialize the `StreamsClient` object and connect to the exchange server:
```php
use React\EventLoop\Loop;

$loop = Loop::get();

$infinex = new Infinex\StreamsClient($loop, 'wss://stream.infinex.cc');

$infinex -> open() -> then(
	function() {
		echo "Connection successfull\n";
	},
	function($e) {
		echo 'Connection failed: '.$e -> getMessage()."\n";
	}
);
```
If the connection is broken, the `StreamsClient` object will automatically reconnect, re-login and restore all subscriptions. However, certain events, e.g. orderbook update, may take place when we were not connected. Therefore, we can catch and react to connection lost and restore events.
```php
$infinex -> on('open', function() {
    echo "Connected to Infinex streams server\n";
});

$infinex -> on('close', function() {
    echo "Disconnected from Infinex streams server\n";
});
```
To subscribe to a stream or multiple streams we can use the `sub` function. As the first argument, we pass the name of the stream or an array of stream names, as the second argument we pass the callback function that will be called each time the stream receives an event.
```php
$infinex -> sub(
	'BPX/USDT@ticker',
    function($event) {
        echo 'Alert! Market price changed to '.$event -> price."\n";
    }
) -> then(
	function() {
		echo "Subscribed!\n";
    },
    function($e) {
        echo "Failed to subscribe! '.$e->getMessage()."\n";
    }
);
```
To unsubscribe from a stream or multiple streams, use the `unsub` function
```php
$infinex -> unsub('BPX/USDT@ticker') -> then(
	function() {
		echo "Unsubscribed!\n";
    },
    function($e) {
        echo "Failed to unsubscribe! '.$e->getMessage()."\n";
    }
);
```
To use private streams, login to the exchange with your API key first
```php
$infinex -> login('api_key_here') -> then(
	function() {
		echo "Logged in\n";
	},
	function($e) {
		echo "Login error! ".$e -> getMessage()."\n";
	}
);
```