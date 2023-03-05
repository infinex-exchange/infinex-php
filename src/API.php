<?php

namespace Infinex;

use Infinex\Exceptions\InfinexException;
use Infinex\Methods;
use React\Promise\Deferred;

class API {
    private $t;
    private $async;
    private $apiKey;
    
    public $wallet;
    public $spot;
    
    public function __construct($transport, $async = false) {
        $this -> t = $transport;
        $this -> async = $async;
        $this -> apiKey = NULL;
        
        $this -> wallet = new Methods\Wallet($this);
        $this -> spot = new Methods\Spot($this);
    }
    
    public function login($apiKey) {
        $this -> apiKey = $apiKey;
    }
    
    public function request($endpoint, $payload = []) {
        $promise = $this -> t -> request($endpoint, $payload) -> then(
            function($resp) {
                if($resp -> success) {
                    unset($resp -> success);
                    return $resp;
                }
                
                throw new InfinexException($resp -> error);
            }
        );
        
        if($this -> async)
            return $promise;
        
        return \React\Async\await($promise);
    }
    
    public function requestPrv($endpoint, $payload = []) {
        if(!$this -> apiKey) {
            $deferred = new Deferred();
            
            $deferred -> reject(
                new InfinexException('Unauthorized')
            );
            
            if($this -> async)
                return $deferred -> promise();
            else
                return \React\Async\await($deferred -> promise());
        }
        
        $payload['api_key'] = $this -> apiKey;
        
        return $this -> request($endpoint, $payload);
    }
}

?>