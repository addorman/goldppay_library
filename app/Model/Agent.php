<?php

namespace Common\Model;

use Moogula\Database\Eloquent\Model;

class Agent extends Model
{
    protected $fillable = [
        'username', 'nickname'
    ];

}
