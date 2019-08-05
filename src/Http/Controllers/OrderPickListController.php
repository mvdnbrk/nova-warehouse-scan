<?php

namespace Just\Warehouse\Nova\Scan\Http\Controllers;

use Illuminate\Http\Response;
use Just\Warehouse\Models\Order;
use Illuminate\Routing\Controller;

class OrderPickListController extends Controller
{
    /**
     * Show the pick list for a given order.
     *
     * @param  \Just\Warehouse\Models\Order  $order
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Order $order)
    {
        if (! $order->hasPickList()) {
            return response([
                'message' => 'No pick list available.'
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $list = $order->pickList();

        return response([
            'items' => $list->toArray(),
            'count' => $list->pluck('quantity')->sum(),
        ]);
    }
}
