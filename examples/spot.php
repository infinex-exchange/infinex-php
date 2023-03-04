<?php
require '../vendor/autoload.php';

$infinex = new Infinex\API(
    new Infinex\Transport\HTTP('https://api.infinex.cc')
);

try {
    // Get first 50 trading pairs
    var_dump(
        $infinex -> spot -> getMarkets();
    );
    
    // Get next 50 assets supported by exchange
    var_dump(
        $infinex -> spot -> getMarkets(50);
    );
    
    // Get first 50 trading pairs but only quoted to USDT
    var_dump(
        $infinex -> spot -> getMarkets(0, 'USDT');
    );
    
    // Get current market price of BPX/USDT
    echo $infinex -> spot -> getMarket('BPX/USDT') -> price;
    
    // Get extended information, e.g. daily volume of BTC/USDT
    echo $infinex -> spot -> getMarket('BTC/USDT', true) -> vol_base;
    
    // Search for markets of tokens with a name containing "ther"
    var_dump(
        $infinex -> spot -> searchMarkets('ther')
    );
    
    // Search for markets of tokens with a name containing "ther" but only quoted to BPX
    var_dump(
        $infinex -> spot -> searchMarkets('ther', 0, 'BPX')
    );
    
    // Get orderbook for ETH/USDT
    var_dump(
        $infinex -> spot -> getOrderBook('ETH/USDT')
    );
    
    // Get highest bid of ETH/USDT
    echo $infinex -> spot -> getOrderBook('ETH/USDT') -> bids[0];
    
    // Get lowest ask of ETH/USDT
    echo $infinex -> spot -> getOrderBook('ETH/USDT') -> asks[0];
    
    // Get last market trades of BTC/USDT
    var_dump(
        $infinex -> spot -> getTrades('BTC/USDT')
    );
    
    // Get 1D candlesticks (OHLCV) for BTC/USDT between two timestamps
    var_dump(
        $infinex -> spot -> getCandleSticks('BTC/USDT', '1D', 1677610000, 1677710000)
    );
    
    // Login to exchange before using account-related methods
    $infinex -> login('my_api_key');
    
    // Get first 50 of my open orders
    var_dump(
        $infinex -> spot -> getOpenOrders()
    );
    
    // Get first 50 of my open orders for BPX/USDT
    var_dump(
        $infinex -> spot -> getOpenOrders(0, 'BPX/USDT')
    );
    
    // Get first 50 of my orders history for BPX/USDT
    var_dump(
        $infinex -> spot -> getOrdersHistory(0, 'BPX/USDT')
    );
    
    // Get first 50 of my trades history for BPX/USDT
    var_dump(
        $infinex -> spot -> getTradesHistory(0, 'BPX/USDT')
    );
    
    // Buy 1000 BPX by market order
    $infinex -> spot -> postOrder('BPX/USDT', 'BUY', 'MARKET', 'FOK', null, '1000');
    
    // Buy BPX for 3 USDT by market order
    $infinex -> spot -> postOrder('BPX/USDT', 'BUY', 'MARKET', 'FOK', null, null, null, '3');
    
    // Sell 0.001 BTC with limit price 20000 USDT, store order id for further use
    $obid = $infinex -> spot -> postOrder('BTC/USDT', 'SELL', 'LIMIT', 'GTC', '20000', '0.001', 'ACK') -> obid;
    
    // Then cancel previously opened order
    $infinex -> spot -> cancelOrder($obid);
    
    // Perform a raw request to Infinex API
    var_dump(
        $infinex -> request('/spot/markets', [
            'offset' => 0
        ])
    );
}

catch(Infinex\Exceptions\ConnException $e) {
    echo "Connection error: " . $e->getMessage();
}
    
catch(Infinex\Exceptions\InfinexException $e) {
    echo "Error from exchange: " . $e->getMessage();
}    

?>