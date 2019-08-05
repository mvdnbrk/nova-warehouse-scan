<?php

namespace Just\Warehouse\Nova\Scan\Http\Controllers;

use Illuminate\Http\Response;
use Just\Warehouse\Models\Order;
use Illuminate\Routing\Controller;

class OrderFulfilledController extends Controller
{
    /**
     * Mark the given order as fulfilled.
     *
     * @param  \Just\Warehouse\Models\Order  $order
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Order $order)
    {
        if (! $order->hasPickList()) {
            return response([
                'message' => 'This order can not be marked as fulfilled.',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $order->markAsFulfilled();

        return response(null, Response::HTTP_ACCEPTED);
    }
}
