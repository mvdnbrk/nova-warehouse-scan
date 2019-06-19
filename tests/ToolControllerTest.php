<?php

namespace Just\Warehouse\Nova\Scan\Tests;

class ToolControllerTest extends TestCase
{
    /** @test */
    public function it_can_can_return_a_response()
    {
        $this->get('nova-vendor/mvdnbrk/nova-warehouse-scan/endpoint')
             ->assertSuccessful();
    }
}
