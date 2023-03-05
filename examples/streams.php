<?php
require '../vendor/autoload.php';

use React\EventLoop\Loop;

$loop = Loop::get();

$infinex = new Infinex\StreamsClient($loop, 'wss://stream.infinex.cc');

$infinex -> on('open', function() {
    echo "Connected to Infinex streams server\n";
});

$infinex -> on('close', function() {
    echo "Disconnected from Infinex streams server\n";
});

$infinex -> open() -> then(

    function() use($infinex) {
    
        $infinex -> sub(
            [
                'BPX/USDT@orderBook',
                'XCH/USDT@orderBook',
                'BTC/USDT@orderBook'
            ],
            function($event) {
                echo "Order book update!\nPair: ".$event -> pair.', side: '.$event -> side.
                     ', price level: '.$event -> price.', amount: '.$event -> amount."\n\n";
            }
        ) -> then(
            function() {
                echo "Subscribed BPX/USDT@orderBook\n";
            },
            function($e) {
                echo "Subscription error! ".get_class($e).': '.$e->getMessage()."\n";
            }
        );
        
        $infinex -> sub(
            [
                'BPX/USDT@ticker',
                'XCH/USDT@ticker',
                'BTC/USDT@ticker'
            ],
            function($event) {
                echo "Market price change!\nPair: ".$event -> pair.', price: '.$event -> price."\n\n";
            }
        ) -> then(
            function() {
                echo "Subscribed BPX/USDT@ticker\n";
            },
            function($e) {
                echo "Subscription error! ".get_class($e).': '.$e->getMessage()."\n";
            }
        );
        
        $infinex -> login('api_key_here') -> then(
            function() use($infinex) {
                echo "Logged in\n";
                
                $infinex -> sub(
                    'myOrders',
                    function($event) {
                        echo "Private order event\n".var_dump($event)."\n";
                    }
                ) -> then(
                    function() {
                        echo "Subscribed myOrders\n";
                    },
                    function($e) {
                        echo "Subscription error! ".get_class($e).': '.$e->getMessage()."\n";
                    }
                );
                
            },
            function($e) {
                echo "Login error! ".get_class($e).': '.$e->getMessage()."\n";
            }
        );
        
    },
    
    function($e) {
        echo "Connecting error! ".get_class($e).': '.$e->getMessage()."\n";
    }
    
);

$loop -> run();

?>