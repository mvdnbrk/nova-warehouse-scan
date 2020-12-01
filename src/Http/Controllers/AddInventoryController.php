<?php

namespace Just\Warehouse\Nova\Scan\Http\Controllers;

use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Just\Warehouse\Exceptions\InvalidGtinException;
use Just\Warehouse\Models\Location;

class AddInventoryController extends Controller
{
    /**
     * Store inventory on the given location.
     *
     * @param \Just\Warehouse\Models\Location $location
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Location $location)
    {
        try {
            $location->addInventory(request('gtin'));
        } catch (InvalidGtinException $e) {
            return response([
                'message' => $e->getMessage(),
                'gtin'    => request('gtin'),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        return response([
            'gtin'        => request('gtin'),
            'location_id' => $location->id,
        ], Response::HTTP_CREATED);
    }
}
