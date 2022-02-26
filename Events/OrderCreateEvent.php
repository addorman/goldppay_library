<?php

namespace App\Events;

use App\Model\Orders;

class OrderCreateEvent
{

    public $order;

    /**
     * Create a new event instance.
     *
     * @param Orders $order
     */
    public function __construct(Orders $order)
    {
        $this->order = $order;
    }

}
