<?php

namespace Just\Warehouse\Nova\Scan\Http\Controllers;

use Illuminate\Routing\Controller;
use Just\Warehouse\Models\Location;
use Just\Warehouse\Exceptions\InvalidGtinException;

class AddInventoryController extends Controller
{
    public function store(Location $location)
    {
        try {
            $location->addInventory(request('gtin'));
        } catch (InvalidGtinException $e) {
            return response([
                'message' => $e->getMessage(),
                'gtin' => request('gtin'),
            ], 422);
        }

        return response([
            'gtin' => request('gtin'),
            'location_id' => $location->id,
        ], 201);
    }
}
