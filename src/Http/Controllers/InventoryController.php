<?php

namespace Just\Warehouse\Nova\Scan\Http\Controllers;

use Illuminate\Routing\Controller;
use Just\Warehouse\Models\Location;

class InventoryController extends Controller
{
    /**
     * Show the inventory on a given location.
     *
     * @param \Just\Warehouse\Models\Location $location
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Location $location)
    {
        $items = $location->inventory()->pluck('gtin');

        return response([
            'items' => $items,
            'count' => $items->count(),
        ]);
    }
}
