<?php

namespace Common\Model;

use Moogula\Database\Eloquent\Model;

class AuthRule extends Model
{

    protected $fillable = [
        'type', 'pid', 'name', 'title', 'icon', 'url', 'condition', 'remark', 
        'ismenu', 'menutype', 'extend', 'py', 'pinyin', 'createtime', 'updatetime', 'weigh', 'status',
    ];

}
