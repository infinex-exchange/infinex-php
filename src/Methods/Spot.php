<?php

namespace Infinex\Methods;

class Spot {
    private $api;
    
    public function __construct($api) {
        $this -> api = $api;
    }
    
    public function getMarkets($offset = 0, $quote = null, $sort = null, $sortDir = null, $extended = false) {
        $endpoint = '/spot/markets';
        if($extended) $endpoint .= '_ex';
        
        $payload = [
            'offset' => $offset
        ];
        
        if(isset($quote)) $payload['quote'] = $quote;
        if(isset($sort)) $payload['quote'] = $sort;
        if(isset($sortDir)) $payload['sort_dir'] = $sortDir;
        
        return $this -> api -> request($endpoint, $payload) -> markets;
    }
    
    public function getMarket($pairid, $extended = false) {
        $endpoint = '/spot/markets';
        if($extended) $endpoint .= '_ex';
        
        return $this -> api -> request($endpoint, [
            'pair' => $pairid
        ]) -> markets[0];
    }
    
    public function searchMarkets($query, $offset = 0, $quote = null, $sort = null, $sortDir = null, $extended = false) {
        $endpoint = '/spot/markets';
        if($extended) $endpoint .= '_ex';
        
        $payload = [
            'offset' => $offset,
            'search' => $query
        ];
        
        if(isset($quote)) $payload['quote'] = $quote;
        if(isset($sort)) $payload['quote'] = $sort;
        if(isset($sortDir)) $payload['sort_dir'] = $sortDir;
        
        return $this -> api -> request($endpoint, $payload) -> markets;
    }
    
    public function getOrderBook($pairid) {
        return $this -> api -> request('/spot/orderbook', [
            'pair' => $pairid
        ]);
    }
    
    public function getTrades($pairid, $offset = 0) {
        return $this -> api -> request('/spot/trades', [
            'offset' => $offset,
            'pair' => $pairid
        ]) -> trades;
    }
    
    public function getCandleSticks($pairid, $res, $from, $to) {
        return $this -> api -> request('/spot/candlestick', [
            'pair' => $pairid,
            'res' => $res,
            'from' => $from,
            'to' => $to
        ]) -> candlestick;
    }
    
    public function getOpenOrders($offset = 0, $pairid = null) {
        $payload = [
            'offset' => $offset
        ];
        
        if(isset($pairid)) $payload['filter_pair'] = $pairid;
        
        return $this -> api -> requestPrv('/spot/open_orders', $payload) -> orders;
    }
    
    public function getOrdersHistory($offset = 0, $pairid = null) {
        $payload = [
            'offset' => $offset
        ];
        
        if(isset($pairid)) $payload['filter_pair'] = $pairid;
        
        return $this -> api -> requestPrv('/spot/orders_history', $payload) -> orders;
    }
    
    public function getTradesHistory($offset = 0, $pairid = null) {
        $payload = [
            'offset' => $offset
        ];
        
        if(isset($pairid)) $payload['filter_pair'] = $pairid;
        
        return $this -> api -> requestPrv('/spot/trades_history', $payload) -> trades;
    }
    
    public function postOrder($pairid, $side, $type, $tif, $price = null, $amount = null, $respType = null, $total = null, $stop = null) {
        $payload = [
            'pair' => $pairid,
            'side' => $side,
            'type' => $type,
            'time_in_force' => $tif
        ];
        
        if(isset($price)) $payload['price'] = $price;
        if(isset($amount)) $payload['amount'] = $amount;
        if(isset($respType)) $payload['resp_type'] = $respType;
        if(isset($total)) $payload['total'] = $total;
        if(isset($stop)) $payload['stop'] = $stop;
        
        return $this -> api -> requestPrv('/spot/open_orders/new', $payload);
    }
    
    public function cancelOrder($obid) {
        $this -> api -> requestPrv('/spot/open_orders/cancel', [
            'obid' => $obid
        ]);
    }
}

?>