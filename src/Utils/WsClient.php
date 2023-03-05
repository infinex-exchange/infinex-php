<?php

namespace Infinex\Utils;

use Infinex\Exceptions\ConnException;
use Infinex\Exceptions\InfinexException;
use React\Promise\Deferred;
use Evenement\EventEmitter;

class WsClient extends EventEmitter {
    private $loop;
    private $url;
    
    private $conn = null;
    private $reconnect;
    private $reconDelay;
    private $pingInterval = null;
    
    private $requestId = 1;
    private $requests = [];
    
    private $customClasses = [];
    
    public function __construct($loop, $url) {
        $this -> loop = $loop;
        $this -> url = $url;
    }
    
    public function open() {
        if($this -> conn) {
            $deferred = new Deferred();
            $deferred -> resolve(null);
            return $deferred -> promise();
        }
            
        $th = $this;
        
        return \Ratchet\Client\connect($this -> url, [], [], $this -> loop) -> then(
            function($conn) use($th) {
                $th -> onOpen($conn);
            },
            function($e) {
                throw new ConnException($e -> getMessage());
            }
        );
    }
    
    public function close($reconnect = false) {
        if(!$this -> conn)
            throw new ConnException('Connection already closed');
        
        $this -> reconnect = $reconnect;
        $this -> conn -> close();
    }
    
    private function reconnect() {
	    if($this -> reconDelay < 20)
            $this -> reconDelay += 1;
		
		$th = $this;
		
        $this -> loop -> addTimer(
	        $this -> reconDelay,
	        function() use($th) {
		        $th -> open() -> catch(
			        function($e) use($th) {
				        $th -> reconnect();
			        }
		        );
	        }
        );
    }
    
    private function onOpen($conn) {
        $this -> conn = $conn;
        $this -> reconnect = true;
        $this -> reconDelay = 0;
        
        $th = $this;
        
        $conn -> on('close', function() use($th) {
            $th -> onClose();
        });
        $conn -> on('message', function($msg) use($th) {
            $th -> onMessage($msg);
        });
        
        $this -> pingInterval = $this -> loop -> addPeriodicTimer(
            5,
            function() use($th) {
                $th -> ping();
            }
        );
        
        $this -> emit('open');
    }
    
    private function onClose($code = null, $reason = null) {
        $this -> conn = null;
                    
        if($this -> pingInterval)
            $this -> loop -> cancelTimer($this -> pingInterval);
                    
        foreach($this -> requests as $reqId => $reqDeferred) {
            $reqDeferred -> reject(
                new ConnException('Connection closed')
            );
            unset($this -> requests[$reqId]);
        }
		            
		$this -> emit('close');
        
		if($this -> reconnect)     
		    $this -> reconnect();
    }
    
    private function onMessage($rawMsg) {
	    $msg = json_decode($rawMsg);
	    
		if($msg -> class == 'resp') {
			if($msg -> success)
				$this -> requests[$msg -> id] -> resolve($msg);
            else
                $this -> requests[$msg -> id] -> reject(
                    new InfinexException($msg -> error)
                );
            
            unset($this -> requests[$msg -> id]);
		}
		
		else if(isset($this -> customClasses[$msg -> class]))
            $this -> customClasses[$msg -> class]($msg);
    }
    
    private function ping() {
        $th = $this;
        
        $this -> req([
            'op' => 'ping'
        ]) -> catch(
            function($e) use($th) {
                $th -> close(true);
            }
        );
    }
    
    protected function req($req) {
        $deferred = new Deferred();
        
        if(!$this -> conn) {
            $deferred -> reject(
                new ConnException('Connection lost')
            );
            return $deferred -> promise();
        }
        
        $id = $this -> requestId;
        $this -> requestId++;
        
        $this -> requests[$id] = $deferred;
        
        $this -> loop -> addTimer(
            5,
            function() use($deferred) {
                $deferred -> reject(
                    new ConnException('Timeout')
                );
            }
        );
        
        $req['id'] = $id;
        $this -> conn -> send(
            json_encode($req)
        );
        
        return $deferred -> promise();
    }
    
    protected function registerCustomClass($class, $callback) {
        $this -> customClasses[$class] = $callback;
    }
}

?>