<?php

namespace Just\Warehouse\Nova\Scan\Tests;

use Just\Warehouse\Models\Order;
use Just\Warehouse\Models\Location;

class OrderFulfilledControllerTest extends TestCase
{
    /** @test */
    public function it_returns_a_404_response_when_trying_to_mark_an_order_as_fulfilled_that_does_not_exist()
    {
        $this->postJson('nova-vendor/mvdnbrk/warehouse-scan/orders/999/fulfilled')->assertNotFound();
    }

    /** @test */
    public function it_returns_a_422_response_when_trying_to_mark_an_order_as_fulfilled_which_is_not_open()
    {
        $order = factory(Order::class)->create();

        $this->postJson('nova-vendor/mvdnbrk/warehouse-scan/orders/'.$order->id.'/fulfilled')->assertStatus(422);
    }

    /** @test */
    public function it_can_be_marked_as_fulfilled()
    {
        $location = factory(Location::class)->create();
        $location->addInventory('1300000000000');
        $order = factory(Order::class)->create();
        $order->addLine('1300000000000');
        $order->process();

        $this->postJson('nova-vendor/mvdnbrk/warehouse-scan/orders/'.$order->id.'/fulfilled')->assertStatus(202);
    }
}
