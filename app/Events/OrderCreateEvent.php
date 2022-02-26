<?php

namespace Common\Events;

use Common\Model\Orders;

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
