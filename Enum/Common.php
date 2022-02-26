<?php

namespace App\Enum;

class Common
{

    /**
     * 状态列表
     */
    public static function getStatusList()
    {
        return array(__('Hidden'), __('Normal'));
    }
}
