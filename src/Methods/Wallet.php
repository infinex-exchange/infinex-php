<?php

namespace Infinex\Methods;

class Wallet {
    private $api;
    
    public function __construct($api) {
        $this -> api = $api;
    }
    
    public function getAssets($offset = 0) {
        return (array) $this -> api -> request('/wallet/assets', [
            'offset' => $offset
        ]) -> assets;
    }
    
    public function getAsset($symbol) {
        return $this -> api -> request('/wallet/assets', [
            'symbols' => [ $symbol ]
        ]) -> assets -> $symbol;
    }
    
    public function searchAssets($query, $offset = 0) {
        return (array) $this -> api -> request('/wallet/assets', [
            'search' => $query,
            'offset' => $offset
        ]) -> assets;
    }
    
    public function getNetworksForAsset($assetid) {
        return (array) $this -> api -> request('/wallet/networks', [
            'asset' => $assetid
        ]) -> networks;
    }
    
    public function getBalances($offset = 0, $extended = false) {
        $endpoint = '/wallet/balances';
        if($extended) $endpoint .= '_ex';
        
        return (array) $this -> api -> requestPrv($endpoint, [
            'offset' => $offset
        ]) -> balances;
    }
    
    public function getBalance($assetid, $extended = false) {
        $endpoint = '/wallet/balances';
        if($extended) $endpoint .= '_ex';
        
        return $this -> api -> requestPrv($endpoint, [
            'symbols' => [ $assetid ]
        ]) -> balances -> $assetid;
    }
    
    public function getTransactions($offset = 0, $assetid = null, $type = null, $status = null) {
        $payload = [
            'offset' => $offset
        ];
        
        if(isset($assetid)) $payload['asset'] = $assetid;
        if(isset($type)) $payload['type'] = $type;
        if(isset($status)) $payload['status'] = $status;
        
        return $this -> api -> requestPrv('/wallet/transactions', $payload) -> transactions;
    }
    
    public function deposit($assetid, $netid) {
        return $this -> api -> requestPrv('/wallet/deposit', [
            'asset' => $assetid,
            'network' => $netid
        ]);
    }
    
    public function getWithdrawalInfo($assetid, $netid) {
        return $this -> api -> requestPrv('/wallet/withdraw/info', [
            'asset' => $assetid,
            'network' => $netid
        ]);
    }
    
    public function validateWithdrawalAddr($assetid, $netid, $address = null, $memo = null) {
        $payload = [
            'asset' => $assetid,
            'network' => $netid
        ];
        
        if(isset($address)) $payload['address'] = $address;
        if(isset($memo)) $payload['memo'] = $memo;
        
        return $this -> api -> requestPrv('/wallet/withdraw/validate', $payload);
    }
    
    public function withdraw($assetid, $netid, $address, $amount, $fee, $memo = null, $adbkName = null) {
        $payload = [
            'asset' => $assetid,
            'network' => $netid,
            'address' => $address,
            'amount' => $amount,
            'fee' => $fee
        ];
        
        if(isset($memo)) $payload['memo'] = $memo;
        if(isset($adbkName)) $payload['adbk_name'] = $adbkName;
        
        return $this -> api -> requestPrv('/wallet/withdraw', $payload) -> xid;
    }
    
    public function cancelWithdrawal($xid) {
        $this -> api -> requestPrv('/wallet/withdraw/cancel', [
            'xid' => $xid
        ]);
    }
    
    public function getAdbkItems($assetid = null, $netid = null) {
        $payload = [];
        
        if(isset($assetid)) $payload['asset'] = $assetid;
        if(isset($netid)) $payload['network'] = $netid;
        
        return $this -> api -> requestPrv('/wallet/addressbook', $payload) -> addressbook;
    }
    
    public function renameAdbkItem($adbkid, $newName) {
        $this -> api -> requestPrv('/wallet/addressbook/rename', [
            'adbkid' => $adbkid,
            'new_name' => $newName
        ]);
    }
    
    public function deleteAdbkItem($adbkid) {
        $this -> api -> requestPrv('/wallet/addressbook/delete', [
            'adbkid' => $adbkid
        ]);
    }
}

?>