<?php

namespace App\Model;

use Moogula\Database\Eloquent\Model;

class Orders extends Model
{
    const ORDER_TYPE_DEPOSIT = 1; // 入金订单
    const ORDER_TYPE_PAYOUT = 2; // 代付订单
    const ORDER_TYPE_SETTLEMENT = 3; // 结算订单

    protected $fillable = [
        'user_id', 'currency', 'product_id', 'order_sn', 'amount', 'claimed_amount', 'charges', 'remark', 'status'
    ];

    public function product()
    {
        return $this->hasOne(Product::class, 'id', 'product_id');
    }

}
