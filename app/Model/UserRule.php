<?php

namespace Common\Model;

use Moogula\Database\Eloquent\Model;

class UserRule extends Model
{

    protected $fillable = [
        'type', 'pid', 'name', 'title', 'icon', 'url', 'condition', 'remark', 
        'ismenu', 'menutype', 'extend', 'py', 'pinyin', 'createtime', 'updatetime', 'weigh', 'status',
    ];

    public function getIsmenuList()
    {
        return ['1' => __('Yes'), '0' => __('No')];
    }

    public function getStatusList()
    {
        return [1 => __('Normal'), 0 => __('Hidden')];
    }
}
