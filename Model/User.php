<?php

namespace App\Model;

use Moogula\Auth\AuthenticatSure;
use Moogula\Database\Eloquent\Model;
use Moogula\Contracts\Auth\Authenticatable as AuthenticatableContract;

class User extends Model implements AuthenticatableContract
{
    use AuthenticatSure;

    protected $fillable = [
        'username', 'nickname'
    ];


    /**
     * 安全设置
     */
    public function secret()
    {
        return $this->hasOne(UserSecret::class, 'id', 'user_id');
    }

}
