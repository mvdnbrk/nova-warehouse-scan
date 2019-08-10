<?php

namespace Just\Warehouse\Nova\Scan\Http\Controllers;

use Illuminate\Http\Response;
use Just\Warehouse\Models\Order;
use Illuminate\Routing\Controller;

class OrderPickListController extends Controller
{
    /**
     * Show a list of open orders.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $orders = Order::open()->orderBy('created_at')->get()->makeHidden([
            'meta',
            'status',
            'updated_at',
            'deleted_at',
            'fulfilled_at',
        ]);

        return response([
            'items' => $orders->toArray(),
            'count' => $orders->count(),
        ]);
    }

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
                'message' => 'No pick list available.',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $list = $order->pickList();

        return response([
            'order_number' => $order->order_number,
            'items' => $list->toArray(),
            'count' => $list->pluck('quantity')->sum(),
        ]);
    }
}
