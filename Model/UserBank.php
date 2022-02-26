<?php

namespace App\Model;

use App\Enum\Currency;
use Moogula\Database\Eloquent\Model;

class UserBank extends Model
{
    protected $fillable = [
        'user_id', 'currency', 'bank_id', 'account_name', 'account_no', 'remark', 'status',
    ];

    public function bank()
    {
        return $this->hasOne(Bank::class, 'id', 'bank_id');
    }

    /**
     *  页面对应的tab列表
     */
    public function getTypeList()
    {
        return Currency::getProductCurrencyList();
    }
}
