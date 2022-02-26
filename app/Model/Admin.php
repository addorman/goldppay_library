<?php

namespace Common\Model;

use Moogula\Auth\AuthenticatSure;
use Moogula\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Moogula\Database\Eloquent\Model;

class Admin extends Model implements AuthenticatableContract
{
    use AuthenticatSure;

    protected $fillable = [
        'avatar', 'nickname'
    ];

    /**
     * Get the name of the unique identifier for the user.
     *
     * @return string
     */
    public function getAuthIdentifierName()
    {
        return $this->getKeyName();
    }

    /**
     * Get the unique identifier for the user.
     *
     * @return mixed
     */
    public function getAuthIdentifier()
    {
        return $this->{$this->getAuthIdentifierName()};
    }
}
