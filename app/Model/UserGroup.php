<?php

namespace Common\Model;

use Moogula\Database\Eloquent\Model;

class UserGroup extends Model
{
    protected $fillable = [
        'name', 'rules', 'status',
    ];

}
