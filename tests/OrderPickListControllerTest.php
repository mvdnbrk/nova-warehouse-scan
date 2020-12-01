<?php

namespace Just\Warehouse\Nova\Scan\Tests;

use Just\Warehouse\Models\Location;
use Just\Warehouse\Models\Order;

class OrderPickListControllerTest extends TestCase
{
    /** @test */
    public function it_can_show_a_list_of_open_orders()
    {
        $location = factory(Location::class)->create();
        $location->addInventory('1300000000000');
        $order = factory(Order::class)->create(['order_number' => '123456']);
        $order->addLine('1300000000000');
        $order->process();

        $response = $this->getJson('nova-vendor/mvdnbrk/warehouse-scan/orders/open');

        $response->assertOk();
        $response->assertJson([
            'items' => [
                [
                    'id'           => $order->id,
                    'order_number' => '123456',
                ],
            ],
            'count' => 1,
        ]);
    }

    /** @test */
    public function it_does_not_show_orders_which_can_not_be_picked()
    {
        $response = $this->getJson('nova-vendor/mvdnbrk/warehouse-scan/orders/open');

        $response->assertOk();
        $response->assertJson([
            'items' => [],
            'count' => 0,
        ]);
    }

    /** @test */
    public function it_returns_a_404_response_when_trying_to_get_a_picklist_for_an_order_that_does_not_exist()
    {
        $this->getJson('nova-vendor/mvdnbrk/warehouse-scan/orders/999/picklist')->assertNotFound();
    }

    /** @test */
    public function it_returns_a_422_response_when_trying_to_get_a_picklist_for_an_order_while_its_not_available()
    {
        $order = factory(Order::class)->create(['order_number' => '123456']);

        $response = $this->getJson('nova-vendor/mvdnbrk/warehouse-scan/orders/'.$order->id.'/picklist');

        $response->assertStatus(422);
        $response->assertJson([
            'order_number' => '123456',
            'message'      => 'This order can not be picked.',
            'status'       => 'created',
        ]);
    }

    /** @test */
    public function it_returns_a_422_response_when_trying_to_get_a_picklist_for_an_order_which_is_on_hold()
    {
        $order = factory(Order::class)->create(['order_number' => '123456']);
        $order->update(['status' => 'hold']);

        $response = $this->getJson('nova-vendor/mvdnbrk/warehouse-scan/orders/'.$order->id.'/picklist');

        $response->assertStatus(422);
        $response->assertJson([
            'order_number' => '123456',
            'message'      => 'This order can not be picked.',
            'status'       => 'on hold',
        ]);
    }

    /** @test */
    public function it_returns_a_422_response_when_trying_to_get_a_picklist_for_an_order_which_is_in_backorder()
    {
        $order = factory(Order::class)->create(['order_number' => '123456']);
        $order->update(['status' => 'backorder']);

        $response = $this->getJson('nova-vendor/mvdnbrk/warehouse-scan/orders/'.$order->id.'/picklist');

        $response->assertStatus(422);
        $response->assertJson([
            'order_number' => '123456',
            'message'      => 'This order can not be picked.',
            'status'       => 'in backorder',
        ]);
    }

    /** @test */
    public function it_can_retrieve_a_picklist()
    {
        $location = factory(Location::class)->create(['name' => 'Test Location']);
        $location->addInventory('1300000000000');
        $order = factory(Order::class)->create(['order_number' => '123456']);
        $order->addLine('1300000000000');
        $order->process();

        $response = $this->getJson('nova-vendor/mvdnbrk/warehouse-scan/orders/'.$order->id.'/picklist');

        $response->assertOk();
        $response->assertJson([
            'order_number' => '123456',
            'items'        => [
                [
                    'gtin'     => '1300000000000',
                    'location' => 'Test Location',
                    'quantity' => 1,
                ],
            ],
            'count'  => 1,
            'status' => 'open',
        ]);
    }
}
