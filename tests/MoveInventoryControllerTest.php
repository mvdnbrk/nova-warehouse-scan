<?php

namespace Just\Warehouse\Nova\Scan\Tests;

use Just\Warehouse\Models\Location;

class MoveInventoryControllerTest extends TestCase
{
    /** @test */
    public function it_returns_a_404_response_when_posting_to_a_location_that_does_not_exist()
    {
        $newLocation = factory(Location::class)->create();

        $this->postJson('nova-vendor/mvdnbrk/warehouse-scan/locations/999/move-inventory', [
            'items' => ['1300000000000'],
            'newLocationId' => $newLocation->id,
        ])->assertNotFound();
    }

    /** @test */
    public function it_returns_a_404_response_when_posting_to_a_new_location_that_does_not_exist()
    {
        $location = factory(Location::class)->create();
        $inventory = $location->addInventory('1300000000000');

        $this->postJson('nova-vendor/mvdnbrk/warehouse-scan/locations/'.$location->id.'/move-inventory', [
            'items' => ['1300000000000'],
            'newLocationId' => 999.,
        ])->assertNotFound();

        $inventory->fresh()->location->is($location);
    }

    /** @test */
    public function it_can_move_inventory_from_one_location_to_another()
    {
        $location1 = factory(Location::class)->create();
        $inventory = $location1->addInventory('1300000000000');
        $location2 = factory(Location::class)->create();

        $response = $this->postJson('nova-vendor/mvdnbrk/warehouse-scan/locations/'.$location1->id.'/move-inventory', [
            'items' => [
                ['gtin' => '1300000000000'],
            ],
            'newLocationId' => $location2->id,
        ]);

        $response->assertStatus(202);
        $inventory->fresh()->location->is($location2);
    }
}
