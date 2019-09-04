<?php

namespace Just\Warehouse\Nova\Scan\Http\Controllers;

use Illuminate\Routing\Controller;
use Just\Warehouse\Models\Location;

class LocationController extends Controller
{
    /**
     * Show the inventory on a given location.
     *
     * @param  \Just\Warehouse\Models\Location  $location
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Location $location)
    {
        return response([
            'name' => $location->name,
            'inventory_count' => $location->inventory->count(),
        ]);
    }
}
