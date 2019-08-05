<?php

namespace Just\Warehouse\Nova\Scan\Tests;

use Just\Warehouse\Models\Location;

class InventoryControllerTest extends TestCase
{
    /** @test */
    public function it_returns_a_404_response_when_trying_to_get_inventory_on_a_location_that_does_not_exist()
    {
        $this->getJson('nova-vendor/mvdnbrk/warehouse-scan/locations/999/inventory')->assertNotFound();
    }

    /** @test */
    public function it_can_show_the_inventory_on_a_location()
    {
        $location = factory(Location::class)->create();
        $location->addInventory('1300000000000');

        $response = $this->getJson('nova-vendor/mvdnbrk/warehouse-scan/locations/'.$location->id.'/inventory');

        $response->assertOk();
        $response->assertJson([
            'items' => [
                '1300000000000',
            ],
            'count' => 1,
        ]);
    }
}
