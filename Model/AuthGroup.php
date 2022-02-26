<?php

namespace App\Model;

use Moogula\Database\Eloquent\Model;

class AuthGroup extends Model
{

    public function getStatusList()
    {
        return [1 => __('Normal'), 0 => __('Hidden')];
    }

}
