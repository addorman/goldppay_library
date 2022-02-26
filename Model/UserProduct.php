<?php

namespace App\Model;

use App\Enum\Currency;
use Moogula\Database\Eloquent\Model;

class UserProduct extends Model
{

    public function product()
    {
        return $this->hasOne(Product::class, 'id', 'product_id');
    }

    /**
     *  页面对应的tab列表
     */
    public function getTypeList()
    {
        return Currency::getProductCurrencyList();
    }
}
