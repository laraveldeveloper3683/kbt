<?php

namespace App\Http\Controllers;

use App\OrderItem;
use Illuminate\Http\Request;
use Auth;
use DB;
use App\User;
use App\Account;
use App\Http\Requests\VendorRequestOrderRequest;
use App\Order;
use App\LocationTime;
use App\Location;
use App\Product;
use App\PurchaseOrder;
use App\PurchaseOrderItem;
use App\State;
use App\Vendor;
use Carbon\Carbon;
use Hash;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth', [
            'except' => [
                'my_order_details',
                'updateOrderDetails'
            ],
        ]);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('dashboard.index');
    }


    public function my_order()
    {
        $pk_users = Auth::user()->pk_users;
        $orders   = DB::table('kbt_orders')->select('kbt_orders.*', 'kbt_order_status.order_status')->join('kbt_order_status', 'kbt_orders.pk_order_status', 'kbt_order_status.pk_order_status')->where('kbt_orders.pk_users', $pk_users)->orderBy('kbt_orders.pk_orders', 'DESC')->get();

        return view('dashboard.customers.index', ['orders' => $orders]);
    }


    public function my_order_details($id = null)
    {
        $orders = Order::where('pk_orders', $id)->with([
            'deliveryOption',
            'orderStatus',
            'customer',
        ])->first();

        if (!$orders) {
            session()->flash('message', 'Order could not be found, please correct errors.');
            session()->flash('level', 'danger');

            return redirect('my-orders');
        }

        $order_items = $orders->order_items;

        $account      = null;
        $locationTime = null;
        if ($orders->deliveryOption->delivery_or_pickup == 'Store Pickup') {
            $locationTime = LocationTime::where('pk_location_times', $orders->pk_location_times)->first();
            // $location = Location::where('pk_locations',$locationTime->pk_locations)->first();
            if ($locationTime) {
                $account = Location::where('pk_locations', $locationTime->pk_locations)->first();
            } else {
                $account = Account::where('pk_account', $orders->pk_locations)->first();
            }
        }

        return view('dashboard.customers.details', compact('orders', 'order_items', 'account', 'locationTime'));
    }

    public function editProfile(Request $request)
    {
        $user_id = Auth::user()->pk_users;
        $user    = DB::table('users')->where('pk_users', $user_id)->first();

        return view('common.customer-profile', ['user' => $user]);
    }

    public function updateProfile(Request $request)
    {
        $user_id = Auth::user()->pk_users;
        // $user = DB::table('users')->where('pk_users',$user_id)->first();
        $validated        = $request->validate(
            [
                'email'      => 'required|email|max:255',
                'first_name' => 'required',
                'last_name'  => 'required',
            ]
        );
        $user             = User::find($user_id);
        $user->first_name = $request->first_name;
        $user->last_name  = $request->last_name;
        $user->email      = $request->email;
        $user->username   = $request->username;
        $user->phone      = $request->phone;
        $user->save();

        return redirect('/customer');
    }

    public function resetCustomer()
    {
        $user_id = Auth::user()->pk_users;
        $user    = User::find($user_id);

        return view('common.customer-reset-password', ['user_id' => $user_id]);
    }

    public function resetUpdate(Request $request)
    {
        $user_id = Auth::user()->pk_users;
        $request->validate(
            [
                'password' => 'required|string|min:6|confirmed',
            ]
        );
        $user           = User::find($user_id);
        $user->password = Hash::make($request->password);
        $user->active   = $request->active;
        $user->save();

        return redirect()->back();
    }

    public function edit_my_order_details(Request $request, $id = null)
    {
        echo "<pre>";
        print_r($request->all());
        die;

        return view('edit_order.blade.php');
    }

    public function cancelOrder(Request $request, $id = null)
    {
        $order                  = Order::find($id);
        $order->pk_order_status = 5;
        $order->save();

        return redirect()->back();
    }


    public function updateOrderDetails(Request $request, $id)
    {
        $order = Order::find($id);

        if ($order && $order->order_items->count() && $request->has('card_messages') && count($request->card_messages)) {
            foreach ($request->card_messages as $key => $value) {
                $orderItem = OrderItem::find($key);
                if ($orderItem) {
                    $orderItem->update([
                        'card_message' => $value,
                    ]);
                }
            }
        }

        return redirect()->back()->with(['success' => 'Order updated successfully.']);
    }
}
