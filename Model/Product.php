<?php

namespace App\Model;

use Moogula\Database\Eloquent\Model;

class Product extends Model
{
    const PRODUCT_TYPE_DEPOSIT = 1; // 入金 deposit
    const PRODUCT_TYPE_PAYOUT = 2; // 代付 payout
    const PRODUCT_TYPE_SETTLEMENT = 3; // 结算 settlement

    const SETTLE_TYPE_PLUS_0 = 1; // T+0
    const SETTLE_TYPE_PLUS_1 = 2; // T+1
    const SETTLE_TYPE_PLUS_2 = 3; // T+2
    
}
