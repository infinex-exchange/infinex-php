<?php

namespace Infinex\Transport;

use Infinex\Exceptions\ConnException;
use Infinex\Utils\WsClient;

class WebSocket extends WsClient {
    public function request($endpoint, $payload) {
        return $this -> open() -> then(
            function() use($endpoint, $payload) {
                return $this -> req([
                    'op' => 'req',
                    'url' => $endpoint,
                    'post' => $payload
                ]) -> then(
                    function($resp) {
                        return $resp -> body;
                    },
                    function($e) {            
                        throw new ConnException($e -> getMessage());
                    }
                );
            }
        );
    } 
}

?>