<?php

namespace Infinex\Transport;

use Infinex\Exceptions\ConnException;
use React\Http\Browser;

class HTTP {
    private $client;
    private $apiUrl;        
    
    public function __construct($apiUrl) {
        $this -> apiUrl = $apiUrl;
        $this -> client = new Browser();
    }
    
    public function request($endpoint, $payload) {
        return $this -> client -> post(
            $this -> apiUrl.$endpoint,
            [ 'Content-Type' => 'application/json' ],
            json_encode($payload)
        ) -> then(
            function($resp) {
                $body = json_decode($resp -> getBody());
                if($body === null)
                    throw new ConnException('Not a valid JSON response');
                return $body;
            },
            function($e) {            
                throw new ConnException($e -> getMessage());
            }
        );
    } 
}

?>