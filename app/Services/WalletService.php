<?php

namespace App\Services;

use App\Models\Wallet;

class WalletService {

    public function getBalance($user_id){
        $wallet = Wallet::where('user_id', $user_id)->orderBy('id', 'desc')->first();
        if($wallet){
            return $wallet->balance;
        }else{
            return 0;
        }
    }
}