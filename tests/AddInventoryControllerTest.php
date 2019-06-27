<?php

namespace Just\Warehouse\Nova\Scan\Tests;

use Just\Warehouse\Models\Location;
use Just\Warehouse\Models\Inventory;

class AddInventoryControllerTest extends TestCase
{
    /** @test */
    public function it_returns_404_response_when_posting_to_a_location_that_does_not_exist()
    {
        $this->postJson('nova-vendor/mvdnbrk/warehouse-scan/locations/999/add-inventory', [
            'gtin' => '1300000000000',
        ])->assertNotFound();

        $this->assertCount(0, Inventory::all());
    }

    /** @test */
    public function it_can_add_inventory_to_a_location()
    {
        $location = factory(Location::class)->create();

        $response = $this->postJson('nova-vendor/mvdnbrk/warehouse-scan/locations/'.$location->id.'/add-inventory', [
            'gtin' => '1300000000000',
        ]);

        $response->assertStatus(201);
        $response->assertJson([
            'gtin' => '1300000000000',
            'location_id' => $location->id,
        ]);
        $this->assertCount(1, $location->inventory);
    }

    /** @test */
    public function it_returns_a_422_response_when_posting_an_empty_gtin_value()
    {
        $location = factory(Location::class)->create();

        $this->postJson('nova-vendor/mvdnbrk/warehouse-scan/locations/'.$location->id.'/add-inventory', [
            'gtin' => '',
        ])->assertStatus(422);

        $this->assertCount(0, Inventory::all());
    }

    /** @test */
    public function it_returns_a_422_response_when_posting_an_invalid_gtin_value()
    {
        $location = factory(Location::class)->create();

        $this->postJson('nova-vendor/mvdnbrk/warehouse-scan/locations/'.$location->id.'/add-inventory', [
            'gtin' => '999',
        ])->assertStatus(422);

        $this->assertCount(0, Inventory::all());
    }
}
