<?php

namespace Just\Warehouse\Nova\Scan\Tests;

use Just\Warehouse\Models\Location;

class LocationControllerTest extends TestCase
{
    /** @test */
    public function it_can_show_the_information_of_a_location()
    {
        $location = factory(Location::class)->create([
            'name' => 'test-123',
        ]);
        $location->addInventory('1300000000000');

        $response = $this->getJson('nova-vendor/mvdnbrk/warehouse-scan/locations/'.$location->id);

        $response->assertOk();
        $response->assertJson([
            'name'            => 'test-123',
            'inventory_count' => 1,
        ]);
    }

    /** @test */
    public function it_returns_a_404_response_when_trying_to_get_a_location_that_does_not_exist()
    {
        $this->getJson('nova-vendor/mvdnbrk/warehouse-scan/location/999')->assertNotFound();
    }
}
