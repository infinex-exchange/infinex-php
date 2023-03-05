<?php
require '../vendor/autoload.php';

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