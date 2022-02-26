<?php

namespace Common\Model;

use Moogula\Database\Eloquent\Model;

class AgentGroup extends Model
{
    protected $fillable = [
        'name', 'rules', 'status',
    ];

}
