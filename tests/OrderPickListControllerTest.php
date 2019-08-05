<?php

namespace Just\Warehouse\Nova\Scan\Tests;

use Just\Warehouse\Models\Order;
use Just\Warehouse\Models\Location;

class OrderPickListControllerTest extends TestCase
{
    /** @test */
    public function it_returns_a_404_response_when_trying_to_get_a_picklist_for_an_order_that_does_not_exist()
    {
        $this->getJson('nova-vendor/mvdnbrk/warehouse-scan/orders/999/picklist')->assertNotFound();
    }

    /** @test */
    public function it_returns_a_422_response_when_trying_to_get_a_picklist_for_an_order_while_its_not_available()
    {
        $order = factory(Order::class)->create();

        $this->getJson('nova-vendor/mvdnbrk/warehouse-scan/orders/'.$order->id.'/picklist')->assertStatus(422);
    }

    /** @test */
    public function it_can_retrieve_a_picklist()
    {
        $location = factory(Location::class)->create(['name' => 'Test Location']);
        $location->addInventory('1300000000000');
        $order = factory(Order::class)->create();
        $order->addLine('1300000000000');
        $order->process();

        $response = $this->getJson('nova-vendor/mvdnbrk/warehouse-scan/orders/'.$order->id.'/picklist');

        $response->assertOk();
        $response->assertJson([
            'items' => [
                [
                    'gtin' => '1300000000000',
                    'location' => 'Test Location',
                    'quantity' => 1,
                ],
            ],
            'count' => 1,
        ]);
    }
}
