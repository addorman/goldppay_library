<?php

namespace Common\Listeners;

use Common\Events\OrderCreateEvent;

class OrderCreateEventListener
{
    /**
     * Create the event listener.
     *
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  OrderCreateEvent  $event
     * @return void
     */
    public function handle(OrderCreateEvent $event)
    {
        return '1211';
        
    }
}
