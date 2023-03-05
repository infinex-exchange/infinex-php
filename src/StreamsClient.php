<?php

namespace Infinex;

use Infinex\Utils\WsClient;

class StreamsClient extends WsClient {
    private $subDb = [];
    private $apiKey = null;
    
    public function __construct($loop, $url) {
        parent::__construct($loop, $url);
        
        $th = $this;
        
        $this -> on('open', function() use($th) {
            if($th -> apiKey)
                $th -> login($th -> apiKey) -> then(
                    function() use($th) {
                        $th -> restoreSubs();
                    }
                );
            else
                $th -> restoreSubs();
        });
        
        $this -> registerCustomClass('data', [$this, 'onEvent']);
    }
    
    public function login($apiKey) {
        $th = $this;
        
        return $this -> req([
            'op' => 'auth',
            'api_key' => $apiKey
        ]) -> then(
            function() use($th, $apiKey) {
                $th -> apiKey = $apiKey;
            }
        );
    }
    
    public function sub($streams, $callback) {
        if(!is_array($streams))
            $streams = [ $streams ];
        
        $th = $this;
        
        return $this -> req([
            'op' => 'sub',
            'streams' => $streams
        ]) -> then(
            function() use($th, $streams, $callback) {
                foreach($streams as $stream)
                    $this -> subDb[$stream] = $callback;
            }
        );
    }
    
    public function unsub($streams) {
        if(!is_array($streams))
            $streams = [ $streams ];
        
        $th = $this;
        
        return $this -> req([
            'op' => 'unsub',
            'streams' => $streams
        ]) -> then(
            function() use($th, $streams) {
                foreach($streams as $stream)
                    unset($th -> subDb[$stream]);
            }
        );
    }
    
    private function restoreSubs() {
        $copy = $this -> subDb;
        $this -> subDb = [];
        
        foreach($copy as $stream => $callback)
            $this -> sub($stream, $callback);
    }
    
    protected function onEvent($event) {
        if(isset($this -> subDb[$event -> stream]))
            $this -> subDb[$event -> stream]($event);
    }
}

?>