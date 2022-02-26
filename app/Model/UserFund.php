<?php

namespace Common\Model;

use Moogula\Database\Eloquent\Model;

class UserFund extends Model
{

    protected $fillable = [
        'currency', 'user_id', 'pending_deposit', 'pending_payout', 'pending_settlement', 'owned_amount',
    ];
}
