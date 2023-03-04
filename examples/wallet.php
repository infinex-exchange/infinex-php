<?php
require '../vendor/autoload.php';

$infinex = new Infinex\API(
    new Infinex\Transport\HTTP('https://api.infinex.cc')
);

try {
    // Get first 50 assets supported by exchange
    var_dump(
        $infinex -> wallet -> getAssets();
    );
    
    // Get next 50 assets supported by exchange
    var_dump(
        $infinex -> wallet -> getAssets(50);
    );
    
    // Get icon of USDT token
    echo $infinex -> wallet -> getAsset('USDT') -> icon_url;
    
    // Search for tokens with a name containing "ther"
    var_dump(
        $infinex -> wallet -> searchAssets('ther')
    );
    
    // Get available networks for BTC deposit or withdrawal
    var_dump(
        $infinex -> wallet -> getNetworksForAsset('BTC')
    );
    
    // Login to exchange before using account-related methods
    
    $infinex -> login('my_api_key');
    
    // Get first 50 wallet balances
    var_dump(
        $infinex -> wallet -> getBalances()
    );
    
    // Get available BPX balance
    echo $infinex -> wallet -> getBalance('BPX') -> avbl;
    
    // Get USDT balance locked in open orders
    echo $infinex -> wallet -> getBalance('USDT') -> locked;
    
    // Get 50 latest transactions
    var_dump(
        $infinex -> wallet -> getTransactions()
    );
    
    // Get latest USDT deposits
    var_dump(
        $infinex -> wallet -> getTransactions(0, 'USDT', 'DEPOSIT')
    );
    
    // Get latest withdrawals of any token, which was canceled
    var_dump(
        $infinex -> wallet -> getTransactions(0, null, 'WITHDRAWAL', 'CANCELED')
    );
    
    // Check is USDT ERC-20 deposits operating correctly
    echo $infinex -> wallet -> deposit('USDT', 'ETH') -> operating;
    
    // Check how much confirmations is needed for BPX deposit
    echo $infinex -> wallet -> deposit('BPX', 'BPX') -> confirms_target;
    
    // Get deposit address for USDT TRC-20
    echo $infinex -> wallet -> deposit('USDT', 'TRX') -> address;
    
    // Get minimal fee for BTC withdrawal
    echo $infinex -> wallet -> getWithdrawalInfo('BTC', 'BTC') -> fee_min;
    
    // Check is withdrawal address valid
    echo $infinex -> wallet -> validateWithdrawalAddr('BTC', 'BTC', '3AA00000000000000000000000000000') -> valid_address;
    
    // Withdraw 5 USDT using TRC-20 network with fee 3.50 USDT
    $xid = $infinex -> wallet -> withdraw('USDT', 'TRX', 'TAA000000000000000000000000', '5', '3.5');
    
    // Then cancel this withdrawal
    $infinex -> cancelWithdrawal($xid);
    
    // Get addresses saved in address book
    var_dump(
        $infinex -> wallet -> getAdbkItems()
    );
    
    // Rename address in address book
    $infinex -> wallet -> renameAdbkItem(5, 'Test name');
    
    // Delete address from address book
    $infinex -> wallet -> deleteAdbkItem(5);
    
    // Perform a raw request to Infinex API
    var_dump(
        $infinex -> request('/wallet/assets', [
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