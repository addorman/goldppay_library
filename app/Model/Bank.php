<?php

namespace Common\Model;

use CommonEnum\Currency;
use Moogula\Database\Eloquent\Model;

class Bank extends Model
{

    /**
     *  页面对应的tab列表
     */
    public function getTypeList()
    {
        return Currency::getProductCurrencyList();
    }
}
