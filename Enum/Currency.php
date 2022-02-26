<?php

namespace App\Enum;

class Currency
{

    /**
     * 返回产品的货币列表
     */
    public static function getProductCurrencyList()
    {
        return array('THB', 'VND', 'USDT', 'IDR', 'BRL', 'INR');
    }
}
