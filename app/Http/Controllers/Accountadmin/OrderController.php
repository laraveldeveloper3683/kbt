<?php

namespace App\Http\Controllers\Accountadmin;

use App\CustomerAddres;
use App\Helper\Helper;
use App\Location;
use App\LocationTime;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Order;
use App\Sale;
use App\OrderItem;
use App\OrderStatus;
use DB;

class OrderController extends Controller
{
    public function index()
    {
        $order_status = OrderStatus::all();
        $orders       = Order::latest()->with([
            'orderStatus',
            'customer',
            'location',
        ])->get();
        //echo "<pre>"; print_r($orders); die;

        return view('accountadmin.orders.index', compact('orders', 'order_status'));
    }

    public function detail($id)
    {
        $orders          = Order::with(['transactions', 'orderStatus', 'customer'])->where('pk_account', 2)
            ->where("pk_orders", $id)->first();
        $items           = OrderItem::where('pk_orders', $id)->get();
        $orderStatus     = OrderStatus::all();
        $customerAddress = CustomerAddres::where('pk_customers', $orders->pk_customers)->latest()
            ->with(['state', 'country'])->first();

        $account      = null;
        $locationTime = null;
        if ($orders->deliveryOption->delivery_or_pickup == 'Store Pickup') {
            $locationTime = LocationTime::where('pk_location_times', $orders->pk_location_times)->first();
            if ($locationTime) {
                $account = Location::where('pk_locations', $locationTime->pk_locations)->with('locationTime')->first();
            }
        }

        return view('accountadmin.orders.detail', compact(
            'orders',
            'items',
            'orderStatus',
            'customerAddress',
            'account',
            'locationTime'
        ));
    }

    public function filter(Request $request)
    {
        $fields = $request->validate([
            'search' => ['required'],
        ]);
        $orders = DB::table('kbt_orders')->where('email', 'LIKE', '%' . $fields["search"] . '%')->orWhere('customer_name', 'LIKE', '%' . $fields["search"] . '%')->orWhere('phone', 'LIKE', '%' . $fields["search"] . '%')->orWhere('arrangement_type', 'LIKE', '%' . $fields["search"] . '%')->get();
        // echo "<pre>"; print_r($orders); die;
        return view('accountadmin.orders.index', ['orders' => $orders, 'search' => $fields["search"]]);
    }

    public function orderStatusUpdate(Request $request)
    {
        $order = Order::find($request->pk_prders);
        if ($order) {
            $order->update([
                'pk_order_status' => $request->pk_order_status,
                'cancel_reason'   => $request->cancel_reason,
            ]);
        }

        if ($request->has('card_messages') && count($request->card_messages) && $order->pk_order_status == 1) {
            foreach ($request->card_messages as $key => $value) {
                $orderItem = OrderItem::find($key);
                if ($orderItem) {
                    $orderItem->update([
                        'card_message' => $value,
                    ]);
                }
            }
        }

        $salecheck = Sale::where('pk_orders', $request->pk_orders)->first();
        if (!empty($salecheck)) {
            $sale = Sale::where('pk_orders', $request->pk_orders)->update(['pk_order_status' => $request->pk_order_status]);
        }

        return redirect()->back()->with(Helper::getAcknowledge('ORDER_STATUS_UPDATE'));
    }

    public function orderByStatus(Request $request)
    {
        $order_status = DB::table('kbt_order_status')->get();

        if (empty($request->pk_order_status)) {
            $orderStatusData = Order::latest()->with([
                'orderStatus',
                'customer',
                'location',
            ])->get();
        } else {
            $orderStatusData = Order::latest()->with([
                'orderStatus',
                'customer',
                'location',
            ])
                ->where('pk_order_status', $request->pk_order_status)->orderBy('pk_orders', 'DESC')->get();
        }

        return view('accountadmin.orders.index', ['orderStatusData' => $orderStatusData,
                                                  'order_status'    => $order_status]);
    }

    public function cancelOrder(Request $request, $id = null)
    {
        $order                  = Order::find($id);
        $order->pk_order_status = 5;
        $order->save();
        return redirect()->back()->with(Helper::getAcknowledge('ORDER_STATUS_UPDATE'));
    }

}
