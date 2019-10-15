<?php

namespace Just\Warehouse\Nova\Scan\Http\Controllers;

use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Just\Warehouse\Models\Location;

class MoveInventoryController extends Controller
{
    /**
     * Move inventory to another location.
     *
     * @param  \Just\Warehouse\Models\Location  $location
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request, Location $location)
    {
        $items = collect($request->items)->pluck('gtin')->all();

        $newLocation = Location::findOrFail($request->newLocationId);

        try {
            $location->moveMany($items, $newLocation);
        } catch (Exception $e) {
            return response([
                'message' => $e instanceof ModelNotFoundException
                    ? 'Invalid gtin value: '.collect($e->getIds())->first()
                    : $e->getMessage(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        return response(null, Response::HTTP_ACCEPTED);
    }
}
