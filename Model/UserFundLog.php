<?php

namespace App\Model;

use App\Enum\Currency;
use Moogula\Database\Eloquent\Model;

class UserFundLog extends Model
{

    protected $fillable = [
        'user_id', 'currency', 'order_sn', 'before_amount', 'amount', 'client_id', 'remark',
    ];

    /**
     *  页面对应的tab列表
     */
    public function getTypeList()
    {
        return Currency::getProductCurrencyList();
    }
}
