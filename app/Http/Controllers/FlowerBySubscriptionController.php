<?php

namespace App\Http\Controllers;

use App\Coupon;
use App\DeliveryOrPickup;
use App\Services\OtherCheckoutService;
use App\State;
use App\Transaction;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use DB;
use App\Customer;
use App\CustomerAddres;
use App\User;
use App\Order;
use App\OrderItem;
use App\Addres;
use App\Account;
use App\Location;
use App\LocationTime;
use Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use net\authorize\api\contract\v1 as AnetAPI;
use net\authorize\api\controller as AnetController;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Validator;
use \Carbon\Carbon;

class FlowerBySubscriptionController extends Controller
{
    public $otherCheckoutService;

    public function __construct()
    {
        $this->otherCheckoutService = new OtherCheckoutService();
    }

    public function index()
    {
        $flowerSubscriptions = DB::table('kbt_flower_subscription')
            ->join('kbt_frequency', 'kbt_flower_subscription.pk_frequency', 'kbt_frequency.pk_frequency')
            ->get();

        return view('flower-by-subscription', ['flowerSubscriptions' => $flowerSubscriptions]);
    }


    public function detail_img($id = null)
    {
        $flowerSubscriptions = DB::table('kbt_flower_subscription')
            ->join('kbt_frequency', 'kbt_flower_subscription.pk_frequency', 'kbt_frequency.pk_frequency')
            ->where('kbt_flower_subscription.pk_flower_subscription', $id)
            ->first();
        return view('flower-by-detail', ['flowerSubscriptions' => $flowerSubscriptions]);
    }

    public function view(Request $request)
    {
        $validated           = $request->validate([
            'flower_subscription' => 'required'
        ]);
        $flowerSubscription  = $request->all();
        $flowerSubscriptions = DB::table('kbt_flower_subscription')
            ->join('kbt_frequency', 'kbt_flower_subscription.pk_frequency', 'kbt_frequency.pk_frequency')
            ->get();
        return view('flower-by-subscription-cart', ['flowerSubscription'  => $flowerSubscription,
                                                    'flowerSubscriptions' => $flowerSubscriptions]);
    }


    public function addToCart(Request $request)
    {

        if ($request->type == 3) {

            $flowerSubscriptions = DB::table('kbt_flower_subscription')
                ->join('kbt_frequency', 'kbt_flower_subscription.pk_frequency', 'kbt_frequency.pk_frequency')
                ->select('pk_flower_subscription', 'frequency', 'price', 'kbt_flower_subscription.description', 'path')
                ->where('pk_flower_subscription', $request->id)
                ->first();

            if (!$flowerSubscriptions) {
                abort(404);
            }
        }

        $oth_cart           = session()->get('oth_cart');
        $oth_total_quantity = session()->get('oth_total_quantity');
        $oth_total_hit      = session()->get('oth_total_hit');

        if ($oth_total_hit == '' || $oth_total_hit == null) {
            $oth_total_hit = 0;
        } else {
            $oth_total_hit += 1;
        }
        session()->put('oth_total_hit', $oth_total_hit);

        // if cart is empty then this the first product

        if (!$oth_cart) {

            if ($request->type == 3) {
                $oth_cart[$oth_total_hit] = [
                    "name"        => $flowerSubscriptions->frequency,
                    "description" => $flowerSubscriptions->description,
                    "quantity"    => 1,
                    "price"       => $flowerSubscriptions->price,
                    "photo"       => $flowerSubscriptions->path,
                    "type"        => 3
                ];
            }


            $oth_total_quantity += 1;
            session()->put('oth_cart', $oth_cart);
            session()->put('oth_total_quantity', $oth_total_quantity);
            session()->put('oth_total_hit', $oth_total_hit + 1);

            $htmlCart = view('_header_cart')->render();

            return response()->json(['msg' => 'Product added to cart successfully!', 'data' => $htmlCart]);
        }

        // if cart not empty then check if this product exist then increment quantity
        if (isset($oth_cart[$oth_total_hit])) {

            $oth_cart[$oth_total_hit]['quantity']++;
            $oth_total_quantity += 1;

            session()->put('oth_cart', $oth_cart);
            session()->put('oth_total_quantity', $oth_total_quantity);
            session()->put('oth_total_hit', $oth_total_hit + 1);

            $htmlCart = view('_header_cart')->render();

            return response()->json(['msg' => 'Product added to cart successfully!', 'data' => $htmlCart]);
        }

        // if item not exist in cart then add to cart with quantity = 1
        if ($request->type == 3) {
            $oth_cart[$oth_total_hit] = [
                "name"        => $flowerSubscriptions->frequency,
                "description" => $flowerSubscriptions->description,
                "quantity"    => 1,
                "price"       => $flowerSubscriptions->price,
                "photo"       => $flowerSubscriptions->path,
                "type"        => 3
            ];
        }
        $oth_total_quantity += 1;

        session()->put('oth_cart', $oth_cart);
        session()->put('oth_total_quantity', $oth_total_quantity);
        session()->put('oth_total_hit', $oth_total_hit + 1);

        $htmlCart = view('_header_cart')->render();
        return response()->json(['msg' => 'Product added to cart successfully!', 'data' => $htmlCart]);
    }


    public function cart()
    {
//        dd(session('oth_cart'));
        return view('othere_cart');
    }

    public function update(Request $request)
    {
        $oth_cart = session()->get('oth_cart');
        if (!isset($oth_cart[$request->id]["quantity"])) {
            $oth_cart[$request->id]["quantity"] = $request->quantity ?? $oth_cart[$request->id]["quantity"];
        }

        if (!isset($oth_cart[$request->id]["card_message"])) {
            $oth_cart[$request->id]["card_message"] = $request->card_message ?? $oth_cart[$request->id]["card_message"];
        }

        $oth_cart[$request->id]["quantity"]     = $request->quantity ?? $oth_cart[$request->id]["quantity"];
        $oth_cart[$request->id]["card_message"] = $request->card_message ?? $oth_cart[$request->id]["card_message"];

        session()->put('oth_cart', $oth_cart);
        $subTotal = $oth_cart[$request->id]['quantity'] * $oth_cart[$request->id]['price'];

        $total = $this->getCartTotal();

        session()->put('oth_total_quantity', array_sum(array_column($oth_cart, 'quantity')));

        session()->forget('oth_checkout_preview');

        $htmlCart = view('_header_cart')->render();

        return response()->json([
            'msg'      => 'Cart updated successfully',
            'data'     => $htmlCart,
            'total'    => $total,
            'subTotal' => $subTotal,
            'totalQty' => session()->get('oth_total_quantity')
        ]);
    }

    public function updateCardMessage(Request $request)
    {
        $oth_cart = session()->get('oth_cart');

        if (!isset($oth_cart[$request->id]["card_message"])) {
            $oth_cart[$request->id]["card_message"] = $request->card_message ?? $oth_cart[$request->id]["card_message"];
        }

        $oth_cart[$request->id]["card_message"] = $request->card_message ?? $oth_cart[$request->id]["card_message"];

        session()->put('oth_cart', $oth_cart);


        return response()->json([
            'msg'          => 'Cart message updated successfully',
            'card_message' => $oth_cart[$request->id]["card_message"]
        ]);
    }

    public function updateCardMessages(Request $request)
    {
        $oth_cart = session()->get('oth_cart');

        if ($request->has('card_messages') && count($request->card_messages)) {
            foreach ($request->card_messages as $key => $card_message) {
                $oth_cart[$key]["card_message"] = $card_message;
            }
        }

        session()->put('oth_cart', $oth_cart);


        return response()->json([
            'success' => true, // 'success' => 'Cart message updated successfully
            'msg'     => 'Cart message updated successfully',
        ]);
    }

    public function remove(Request $request)
    {
        if ($request->id != '') {
            $oth_cart = session()->get('oth_cart');
            if (isset($oth_cart[$request->id])) {
                unset($oth_cart[$request->id]);
                session()->put('oth_cart', $oth_cart);

                $oth_total_quantity = session()->get('oth_total_quantity');
                session()->put('oth_total_quantity', array_sum(array_column($oth_cart, 'quantity')));
            }

            $total = $this->getCartTotal();
            session()->forget('oth_checkout_preview');
            $htmlCart = view('_header_cart')->render();
            return response()->json(['msg'   => 'Product removed successfully', 'data' => $htmlCart,
                                     'total' => $total, 'totalQty' => session()->get('oth_total_quantity')]);

            //session()->flash('success', 'Product removed successfully');
        }
    }

    /**
     * Checkout page
     *
     * @param Request $request
     * @return Application|Factory|View
     */
    public function otherCheckOutPage()
    {
        $deliveryOptions   = DeliveryOrPickup::all();
        $oldData           = session('oth_checkout_preview') ?? [];
        $oldDeliveryOption = null;
        if (isset($oldData['choise_details'])) {
            $oldDeliveryOption = DeliveryOrPickup::where('pk_delivery_or_pickup', @$oldData['choise_details'])->first();
        }

        $view = view('other-checkout-guest', compact(
            'deliveryOptions',
            'oldData',
            'oldDeliveryOption'
        ));

        if (auth()->check()) {
            $user_data = auth()->user();

            $pk_customer_id = @$user_data->pk_customers;
            $primaryAddress = CustomerAddres::where('pk_customers', @$pk_customer_id)
                ->where('pk_address_type', 1)->first();
            $primaryState   = State::where('pk_states', @$primaryAddress->pk_states)->first();
            $billingAddress = CustomerAddres::where('pk_customers', @$pk_customer_id)
                ->where('pk_address_type', 2)->first();
            $billingState   = State::where('pk_states', @$billingAddress->pk_states)->first();

            $view = view('other-checkout', compact(
                'user_data',
                'deliveryOptions',
                'oldData',
                'primaryAddress',
                'billingAddress',
                'primaryState',
                'billingState',
                'oldDeliveryOption'
            ));
        }

        return $view;
    }

    public function otherCheckoutPreviewPost(Request $request)
    {
        if (empty(session('oth_cart'))) {
            session()->flash('message', 'Your Cart Is Currently Empty.');
            session()->flash('level', 'danger');
            return redirect('shop');
        }

        // Customer address create
        if (!auth()->check()) {
            $deliveryOption = DeliveryOrPickup::where('pk_delivery_or_pickup', @$request->choise_details)->first();
            if ($deliveryOption->delivery_or_pickup == 'Delivery') {
                $validator = Validator::make($request->all(), [
                    'first_name'                        => 'required',
                    'last_name'                         => 'required',
                    'item_address'                      => 'required',
                    'item_address.*.shipping_full_name' => 'required',
                    'item_address.*.shipping_phone'     => 'required',
                    'item_address.*.shipping_address'   => 'required',
                ], [], [
                    'item_address.*.shipping_full_name' => 'item address name',
                    'item_address.*.shipping_phone'     => 'item address phone',
                    'item_address.*.shipping_address'   => 'item address',
                ]);
            } else {
                $validator = Validator::make($request->all(), [
                    'first_name' => 'required',
                    'last_name'  => 'required',
                ]);
            }

            if ($validator->fails()) {
                session()->flash('message', 'Order could not be placed, please correct errors. -> ' . $validator->errors()->first());
                session()->flash('level', 'danger');
                return redirect('other-checkout')->withErrors($validator)->withInput();
            }

        } else {
            $user_data                    = auth()->user();
            $request['billing_full_name'] = $request->first_name ?? $user_data->first_name . ' ' . $user_data->last_name;
        }

        $data = $request->except(['_token', '_method']);
        session()->put('oth_checkout_preview', $data);

        return redirect()->route('other-checkout-preview');
    }

    public function otherCheckoutPreview()
    {
        if (!session('oth_checkout_preview') || !session('oth_cart') || !count(session('oth_cart')) || !count(session('oth_checkout_preview'))) {
            session()->flash('message', 'Order could not be previewed, please correct errors.');
            session()->flash('level', 'danger');
            return redirect('other-checkout');
        }

        $data = session('oth_checkout_preview');

        $coupon           = Coupon::where('code', @$data['coupon'])->first();
        $discountCharge   = 0;
        $discountedAmount = 0;

        $couponCharge = explode(" ", @$data['discountCharge']);


        if (isset($data['discountCharge'])) {
            if ($couponCharge[1] === '%') {
                $discountCharge               = $couponCharge[0];
                $discountedAmount             = $data['amount'] * $discountCharge / 100;
                $data['coupon_discount_type'] = 'percent';
            } elseif ($couponCharge[0] === '$') {
                $discountCharge               = $couponCharge[1];
                $discountedAmount             = $couponCharge[1];
                $data['coupon_discount_type'] = 'fixed';
            } else {
                $discountCharge               = $data['discountCharge'][0];
                $discountedAmount             = $data['amount'] - $discountCharge;
                $data['coupon_discount_type'] = $data['discountCharge'][1];
            }
        }

        // Total amount after discount
        $amountAfterDiscount = $data['amount'] - $discountedAmount;

        // Delivery option
        $deliveryOption = DeliveryOrPickup::where('pk_delivery_or_pickup', @$data['choise_details'])->first();

        $cartItems = session('oth_cart');

        $deliveryCharge     = 0;
        $sameAsBilling      = 0;
        $duplicateAddresses = [];
        if (isset($data['item_address']) && count($data['item_address'])) {
            foreach ($data['item_address'] as $key => $item_address) {
                $address = $item_address['shipping_address'] . ' ' . $item_address['shipping_address_1'] . ' ' . $item_address['shipping_city'] . ' ' . $item_address['shipping_state_name'] . ' ' . $item_address['shipping_zip'];
                if ($item_address['same_as_billing'] == 0 && !in_array($address, $duplicateAddresses)) {
                    $sameAsBilling  = 0;
                    $deliveryCharge += $item_address['delivery_charge'];
                } else {
                    $sameAsBilling = 1;
                }

                $duplicateAddresses[$key] = $address;
            }
        }

        if ($deliveryCharge <= 0 && isset($data['deleveryCast1'])) {
            $deliveryCharge += $data['deleveryCast1'];
        }

        if ($cartItems && count($cartItems) > 0 && count($cartItems) == 1) {
            $firstItemKey          = array_key_first($cartItems);
            $data['delivery_date'] = $cartItems[$firstItemKey]['delivery_date'] ?? null;
            $data['deleveryCast1'] = $cartItems[$firstItemKey]['delivery_charge'] ?? null;
        }

        $location     = Location::where('pk_locations', $data['pk_locations'])->first();
        $locationTime = LocationTime::where('pk_location_times', $data['pk_location_times'])->first();


        $view = view('other-checkout-guest-preview', compact(
            'data',
            'coupon',
            'discountCharge',
            'discountedAmount',
            'amountAfterDiscount',
            'deliveryOption',
            'cartItems',
            'deliveryCharge',
            'sameAsBilling',
            'location',
            'locationTime'
        ));

        if (auth()->check()) {
            $view = view('other-checkout-auth-preview', compact(
                'data',
                'coupon',
                'discountCharge',
                'discountedAmount',
                'amountAfterDiscount',
                'deliveryOption',
                'cartItems',
                'deliveryCharge',
                'sameAsBilling',
                'location',
                'locationTime'
            ));
        }

        return $view;
    }

    public function otherCheckoutPaymentPost(Request $request)
    {
        if (empty(session('oth_cart'))) {
            session()->flash('message', 'Your Cart Is Currently Empty.');
            session()->flash('level', 'danger');
            return redirect('shop');
        }

        if (!session('oth_checkout_preview') || !session('oth_cart')
            || !count(session('oth_cart')) || !count(session('oth_checkout_preview'))) {
            session()->flash('message', 'Cart or preview data could not be found, please correct errors!');
            session()->flash('level', 'danger');
            return redirect('other-checkout');
        }


        // Customer address create
        if (!auth()->check()) {
            $validator = Validator::make($request->all(), [
                'phone' => 'required',
                'email' => 'required',
            ]);

            if ($validator->fails()) {
                session()->flash('message', 'Order could not be placed, please correct errors. -> ' . $validator->errors()->first());
                session()->flash('level', 'danger');
                return redirect('other-checkout-preview')->withErrors($validator)->withInput();
            }

        } else {
            $user_data                    = auth()->user();
            $request['billing_full_name'] = $request->first_name ?? $user_data->first_name . ' ' . $user_data->last_name;
        }

        $request_data = $request->except(['_token', '_method']);
        $data         = session('oth_checkout_preview');
        // merge request data with session data
        $data = array_merge($data, $request_data);
        session()->put('oth_checkout_preview', $data);

        $cartData = session('oth_cart');

        if ($request->has('card_messages') && count($request->card_messages)) {
            foreach ($request->card_messages as $key => $card_message) {
                $cartData[$key]['card_message'] = $card_message;
            }
        }

        session()->put('oth_cart', $cartData);

        return redirect()->route('other-checkout-payment');
    }

    public function otherCheckoutPayment()
    {
        if (!session('oth_checkout_preview') || !session('oth_cart') || !count(session('oth_cart')) || !count(session('oth_checkout_preview'))) {
            session()->flash('message', 'Cart or preview data could not be found, please correct errors!');
            session()->flash('level', 'danger');
            return redirect('other-checkout');
        }

        $data = session('oth_checkout_preview');

        $page = view('other-checkout-guest-payment', compact('data'));

        if (auth()->check()) {
            $user_data      = auth()->user();
            $kbt_address    = CustomerAddres::where('pk_customers', @$user_data->pk_customers)->get();
            $billingAddress = CustomerAddres::where('pk_customers', @$user_data->pk_customers)
                ->where('pk_address_type', 2)->first();
            $billingState   = State::where('pk_states', @$billingAddress->pk_states)->first();
            $page           = view('other-checkout-payment', compact(
                'data',
                'kbt_address',
                'billingAddress',
                'billingState',
                'user_data'
            ));
        }

        return $page;
    }

    public function other_checkout_guest(Request $request)
    {
        try {
            DB::beginTransaction();

            if (empty(session('oth_cart'))) {
                session()->flash('message', 'Your Cart Is Currently Empty.');
                session()->flash('level', 'danger');
                return redirect('shop');
            }

            // Customer address create
            [
                $customer,
                $pk_customer_id,
                $primaryAddress,
                $billingAddress,
                $validator
            ] = $this->otherCheckoutService->otherCheckoutForGuest($request);


            [
                $get_order,
                $save_order
            ] = $this->otherCheckoutService->otherCheckoutGuestStore($request, $pk_customer_id);

            // Forget session data
            $this->otherCheckoutService->removeOtherCheckoutSession();

            DB::commit();

            // Set success message
            session()->flash('message', 'Order has been placed successfully!');
            session()->flash('level', 'success');

            return redirect('thank-you/' . $get_order->pk_orders);
        } catch (ValidationException $exception) {
            DB::rollBack();
//            session()->flash('message', 'Order could not be placed, please correct errors.');
            session()->flash('message', $exception->getMessage() . ', please correct errors.');
            session()->flash('level', 'danger');
            return redirect('other-checkout')->withInput();
        } catch (\Exception $exception) {
//            dd($exception->getMessage(), $exception->getTraceAsString(), $exception->getLine());
            session()->flash('message', 'Order could not be placed, please correct errors -> ' . $exception->getMessage());
            session()->flash('level', 'danger');
            return redirect('other-checkout')->withInput();
        }
    }

    public function other_checkout(Request $request)
    {
        try {
            DB::beginTransaction();

            if (empty(session('oth_cart'))) {
                session()->flash('message', 'Your Cart Is Currently Empty.');
                session()->flash('level', 'danger');
                return redirect('shop');
            }

            // Customer address create
            [
                $customer_data1,
                $pk_user_id,
                $pk_customer_id,
                $primaryAddress,
                $billingAddress,
                $validator
            ] = $this->otherCheckoutService->otherCheckoutForAuth($request);


            [
                $get_order,
                $save_order
            ] = $this->otherCheckoutService->otherCheckoutStore($request, $pk_user_id, $pk_customer_id);

            // Forget session data
            $this->otherCheckoutService->removeOtherCheckoutSession();

            DB::commit();

            // Set success message
            session()->flash('message', 'Order has been placed successfully!');
            session()->flash('level', 'success');

            return redirect('thank-you/' . $get_order->pk_orders);
        } catch (ValidationException $exception) {
            DB::rollBack();
//            session()->flash('message', 'Order could not be placed, please correct errors.');
            session()->flash('message', $exception->getMessage() . ', please correct errors.');
            session()->flash('level', 'danger');
            return redirect('other-checkout')->withInput();
        } catch (\Exception $exception) {
//            dd($exception->getMessage(), $exception->getTraceAsString(), $exception->getLine());
            session()->flash('message', 'Order could not be placed, please correct errors -> ' . $exception->getMessage());
            session()->flash('level', 'danger');
            return redirect('other-checkout')->withInput();
        }
    }

    public function other_checkouts(Request $request)
    {
        $store = Location::where('pk_account', 2)->latest()->first();

        $output  = ['html' => null];
        $output1 = ['storename' => null, 'kilomiter' => null, 'storeName' => null, 'taxRate' => null];

        // Get the tax rate
        $getDes      = urlencode("{$store->address}, {$store->address_1}, {$store->city}, {$store->zip}");
        $getDes1     = urlencode("{$request->address}, {$request->address_1}, {$request->city}, {$request->postal_code}");
        $shippingurl = "https://maps.googleapis.com/maps/api/distancematrix/json";
        $params      = [
            'destinations' => $getDes1,
            'origins'      => $getDes,
            'key'          => 'AIzaSyAB80hPTftX9xYXqy6_NcooDtW53kiIH3A',
            'units'        => 'imperial'
        ];

        $client    = new Client();
        $response  = $client->get($shippingurl, ['query' => $params])->getBody();
        $responses = json_decode($response, true);
        $distance  = isset($responses['rows'][0]['elements'][0]['distance']) ? $responses['rows'][0]['elements'][0]['distance']['text'] : null;

        if ($distance !== null) {
            $output1['kilomiter'] .= "{$distance}a{$store->pk_locations}b";
        }
        // End of get the tax rate


        // Get the store IDS
        $aa1               = str_replace("m", "", str_replace("b", ",", str_replace("a", ":", str_replace(" ", "", str_replace(",", "", str_replace("km", "", implode(" ", $output1)))))));
        $convert_to_array1 = explode(",", $aa1);
        $linksArray1       = array_filter($convert_to_array1);
        $tttt1             = array_unique($linksArray1);

        $tttt2 = str_replace(".", "", $tttt1);
        sort($tttt2);
        $str1 = preg_replace("/[^a-zA-Z 0-9]+/", ",", $tttt2);

        sort($str1);
        $str12   = preg_replace("/[^a-zA-Z 0-9]+/", ",", $str1);
        $result1 = array_filter($str12);

        $store_id = [];
        if (isset($result1[0])) {
            $st1         = $result1[0];
            $locationId1 = explode(",", $st1);
            $store_id[]  = $locationId1[1];
        }
        if (isset($result1[1])) {
            $st2         = $result1[1];
            $locationId2 = explode(",", $st2);
            $store_id[]  = $locationId2[1];
        }
        if (isset($result1[2])) {
            $st3         = $result1[2];
            $locationId3 = explode(",", $st3);
            $store_id[]  = $locationId3[1];
        }
        // End of get the store IDS


        // Set the store info and tax rate into HTML
        foreach ($store_id as $st) {
            $storecity = Location::where('pk_locations', $st)->first();
            $store1    = DB::table('kbt_location_times')->where('pk_locations', $st)->get();

            $getDes      = urlencode("{$storecity->address}, {$storecity->address_1}, {$storecity->city}, {$storecity->zip}");
            $getDes1     = urlencode("{$request->address}, {$request->address_1}, {$request->city}, {$request->postal_code}");
            $shippingurl = "https://maps.googleapis.com/maps/api/distancematrix/json?destinations=$getDes1&origins=$getDes&key=AIzaSyAB80hPTftX9xYXqy6_NcooDtW53kiIH3A&units=imperial";
            $result      = file_get_contents($shippingurl);
            $responses   = json_decode($result, true);
            $distance1   = $responses['rows'][0]['elements'][0]['distance']['text'] ?? null;

            if ($distance1 !== null) {
                $output['html'] .= '
        <div class="col-md-12 mb-3 store1">
            <div class="row">
                <div class="col-md-12"><h6>' . $storecity->location_name . '</h6></div>
                <div class="col-md-12"><p>' . $distance1 . '</p></div>
                <div class="col-md-12">
                    <p>' . $storecity->address . ' ,' . $storecity->address_1 . ' ,' . $storecity->city . ' ,' . $storecity->zip . ' ,' . $storecity->state_name . ' ,' . $storecity->country_name . '</p>
                </div>
            </div>
            <div class="selectTimeItem">
                <div class="row">';
                foreach ($store1 as $data1) {
                    $output['html'] .= '
                <div class="col-md-10">
                    Day - ' . $data1->day . ' , ' . date('h:i A', strtotime($data1->open_time)) . ' - ' . date('h:i A', strtotime($data1->close_time)) . '
                </div>
                <div class="col-md-2">
                    <input type="radio" required name="store_id" value="' . $data1->pk_location_times . '/' . $store->pk_locations . '" value="store"> Select
                </div>';
                }
                $output['html'] .= '</div></div></div>';
            }
        }

        return response()->json($output);
    }

    public function other_checkoutss(Request $request)
    {
        $store   = Location::where('pk_account', 2)->latest()->first();
        $output  = ['html'      => null, 'Estimated_Delivery_Time' => 0, 'cost' => 0, 'storename' => null,
                    'kilomiter' => null, 'storeName' => null, 'taxRate' => 0, 'pk_location' => null];
        $output2 = ['html' => null];
        $output1 = ['storename' => null, 'kilomiter' => null, 'storeName' => null, 'taxRate' => null];

        // Get the tax rate
        $getDes  = urlencode("{$store->address}, {$store->city}");
        $getDes1 = urlencode("{$request->address}, {$request->city}");

        $shippingurl = "https://maps.googleapis.com/maps/api/distancematrix/json";
        $params      = [
            'destinations' => $getDes1,
            'origins'      => $getDes,
            'key'          => 'AIzaSyAB80hPTftX9xYXqy6_NcooDtW53kiIH3A',
            'units'        => 'imperial'
        ];

        $client    = new Client();
        $response  = $client->get($shippingurl, ['query' => $params])->getBody();
        $responses = json_decode($response, true);
        $distance  = isset($responses['rows'][0]['elements'][0]['distance']) ? $responses['rows'][0]['elements'][0]['distance']['text'] : null;

        if ($distance !== null) {
            $output2['html']      .= $distance;
            $output1['kilomiter'] .= "{$distance}a{$store->pk_locations}b";
        }
        // End of get the tax rate

        // Get the store IDS
        $aa1               = str_replace("m", "", str_replace("b", ",", str_replace("a", ":", str_replace(" ", "", str_replace(",", "", str_replace("mi", "", implode(" ", $output1)))))));
        $convert_to_array1 = explode(",", $aa1);
        $linksArray1       = array_filter($convert_to_array1);
        $tttt1             = array_unique($linksArray1);

        $tttt2 = str_replace(".", "", $tttt1);
        sort($tttt2);
        $str1 = preg_replace("/[^a-zA-Z 0-9]+/", ",", $tttt2);

        sort($str1);
        $str12      = preg_replace("/[^a-zA-Z 0-9]+/", ",", $str1);
        $result1    = array_filter($str12);
        $locationId = explode(",", $result1[0]);

        $storeName                         = DB::table('kbt_locations')->where('pk_locations', $locationId[1])->first();
        $output['storeName']               = $storeName->location_name;
        $output['storeCity']               = $storeName->city;
        $output['taxRate']                 = $storeName->tax_rate;
        $output['pk_location']             = $storeName->pk_locations;
        $dd                                = Carbon::now()->addDays($storeName->Estimated_Delivery_Time);
        $output['Estimated_Delivery_Time'] = date("d-M-Y", strtotime($dd));

        $aa = explode(" ", str_replace(",", "", str_replace("mi", "", implode(" ", $output2))));

        $linksArray = array_filter($aa);

        $tttt = array_unique($linksArray);
        sort($tttt);
        $result = array_filter($tttt);
        // End of get the store IDS

        // Get delivery cost
        $deleveryCast = DB::table('kbt_delivery_charges')
            ->where('miles_from', '<', $result[0])
            ->where('miles_to', '>', $result[0])
            ->get();

        foreach ($deleveryCast as $delCast) {
            $output['cost'] = !empty($delCast->cost) ? $delCast->cost : '0';
        }


        return response()->json($output);
    }

    public function otherCheckoutPickupAddressByDB(Request $request)
    {
        $tLat = $request->lat; // '33.6496252';
        $tLng = $request->lng; // '-117.9190418';

        $output = ['html' => null];


        if ($tLat && $tLng) {
            $stores = Location::query()->where('pk_account', 2)
                ->where('lat', '!=', null)
                ->where('lng', '!=', null);

            $stores = $stores->selectRaw(
                'kbt_locations.*, ST_Distance_Sphere(point(kbt_locations.lng, kbt_locations.lat), point(?, ?)) * .000621371192 as calculated_distance',
                [$tLng, $tLat]
            );
            // get nearest 5 stores
            $stores = $stores->orderBy('calculated_distance')->take(5);

            $output['html'] = view('pickup_address_response', compact('stores'))->render();
        }

        if (!$output['html']) {
            $output['message'] = "Sorry, we don't have pickup point to your area!";

            return response()->json($output, 404);
        }

        $output['message'] = "Success";

        return response()->json($output);
    }

    public function otherCheckoutPickupAddressByGoogle(Request $request)
    {
        $stores = Location::where('pk_account', 2)->whereNotNull('lat')->whereNotNull('lng')->cursor();

        $tLat = $request->lat; // '33.6496252';
        $tLng = $request->lng; // '-117.9190418';

        $output     = ['html' => null];
        $withinDist = 25; // how many miles to search within - default 25

        if ($tLat && $tLng) {
            // loop and get 5 nearest stores by google api distance matrix
            foreach ($stores as $store) {
                // get 5 nearest stores
                $getDes      = "{$store->lat},{$store->lng}";
                $getDes1     = "{$tLat},{$tLng}";
                $shippingurl = "https://maps.googleapis.com/maps/api/distancematrix/json";
                $params      = [
                    'destinations' => $getDes1,
                    'origins'      => $getDes,
                    'key'          => 'AIzaSyAB80hPTftX9xYXqy6_NcooDtW53kiIH3A',
                    'units'        => 'imperial'
                ];

                $client    = new Client();
                $response  = $client->get($shippingurl, ['query' => $params])->getBody();
                $responses = json_decode($response, true);
                $distance  = isset($responses['rows'][0]['elements'][0]['distance']) ? $responses['rows'][0]['elements'][0]['distance']['text'] : null;

                $explodeDistance = null;
                if ($distance) {
                    $explodeDistance = explode(' ', $distance)[0];
                }

                // check if calcDistance is not null and within 25 miles
                if ($explodeDistance && $explodeDistance <= $withinDist) {
                    // Get the store times
                    $locationTime = LocationTime::where('pk_locations', $store->pk_locations)->first();

                    // Set the store info and tax rate into HTML
                    $output['html'] .= '
        <div class="col-md-12 mb-3 store1" id="pickupStore-' . $store->pk_locations . '">
            <div class="row">
                <div class="col-md-12"><h6>' . $store->location_name . '</h6></div>
                <div class="col-md-12"><p>' . $distance . '</p></div>
                <div class="col-md-12">
                    <p>' . $store->address . ' ,' . $store->address_1 . ' ,' . $store->city . ' ,' . $store->zip . ' ,' . $store->state_name . ' ,' . $store->country_name . '</p>
                </div>
            </div>
            <div class="selectTimeItem">
                <div class="row">';
                    $output['html'] .= '
                <div class="col-md-10">
                    Day - ' . $locationTime->day . ' , ' . date('h:i A', strtotime($locationTime->open_time)) . ' - ' . date('h:i A', strtotime($locationTime->close_time)) . '
                </div>
                <div class="col-md-2">
                    <input type="radio" required name="store_id" value="' . $locationTime->pk_location_times . '/' . $store->pk_locations . '"
                    data-taxRate="' . $store->tax_rate . '" data-storeId="' . $store->pk_locations . '"
                    data-locationTime="' . $locationTime->pk_location_times . '" class="pickup-store-checkbox"
                    id="pickup-store-checkbox-' . $store->pk_locations . '"
                    data-distance="' . $distance . '" data-calcDistance="' . $explodeDistance . '"> Select
                </div>';
                    $output['html'] .= '</div></div></div>';
                }
            }
        }

        if (!$output['html']) {
            $output['message'] = "Sorry, we don't have pickup point to your area!";

            return response()->json($output, 404);
        }

        $output['message'] = "Success";

        return response()->json($output);
    }

    public function otherCheckoutPickupAddressOld(Request $request)
    {
        $store = Location::where('pk_account', 2)->latest()->first();

        $output  = ['html' => null];
        $output1 = ['storename' => null, 'kilomiter' => null, 'storeName' => null, 'taxRate' => null];

        // Get the tax rate
        $getDes      = "{$store->lat},{$store->lng}";
        $getDes1     = "{$request->lat},{$request->lng}";
        $shippingurl = "https://maps.googleapis.com/maps/api/distancematrix/json";
        $params      = [
            'destinations' => $getDes1,
            'origins'      => $getDes,
            'key'          => 'AIzaSyAB80hPTftX9xYXqy6_NcooDtW53kiIH3A',
            'units'        => 'imperial'
        ];

        $client    = new Client();
        $response  = $client->get($shippingurl, ['query' => $params])->getBody();
        $responses = json_decode($response, true);
        $distance  = isset($responses['rows'][0]['elements'][0]['distance']) ? $responses['rows'][0]['elements'][0]['distance']['text'] : null;

        if ($distance !== null) {
            $output1['kilomiter'] .= "{$distance}a{$store->pk_locations}b";
        }
        // End of get the tax rate

        // Get the store IDS
        $aa1               = str_replace("m", "", str_replace("b", ",", str_replace("a", ":", str_replace(" ", "", str_replace(",", "", str_replace("km", "", implode(" ", $output1)))))));
        $convert_to_array1 = explode(",", $aa1);
        $linksArray1       = array_filter($convert_to_array1);
        $tttt1             = array_unique($linksArray1);

        $tttt2 = str_replace(".", "", $tttt1);
        sort($tttt2);
        $str1 = preg_replace("/[^a-zA-Z 0-9]+/", ",", $tttt2);

        sort($str1);
        $str12   = preg_replace("/[^a-zA-Z 0-9]+/", ",", $str1);
        $result1 = array_filter($str12);

        $store_id = [];
        if (isset($result1[0])) {
            $st1         = $result1[0];
            $locationId1 = explode(",", $st1);
            $store_id[]  = $locationId1[1];
        }
        if (isset($result1[1])) {
            $st2         = $result1[1];
            $locationId2 = explode(",", $st2);
            $store_id[]  = $locationId2[1];
        }
        if (isset($result1[2])) {
            $st3         = $result1[2];
            $locationId3 = explode(",", $st3);
            $store_id[]  = $locationId3[1];
        }
        // End of get the store IDS

        // Set the store info and tax rate into HTML
        foreach ($store_id as $st) {
            $storecity = Location::where('pk_locations', $st)->first();
            $store1    = DB::table('kbt_location_times')->where('pk_locations', $st)->get();

            $getDes      = "{$store->lat},{$store->lng}";
            $getDes1     = "{$request->lat},{$request->lng}";
            $shippingurl = "https://maps.googleapis.com/maps/api/distancematrix/json?destinations=$getDes1&origins=$getDes&key=AIzaSyAB80hPTftX9xYXqy6_NcooDtW53kiIH3A&units=imperial";
            $result      = file_get_contents($shippingurl);
            $responses   = json_decode($result, true);
            $distance1   = $responses['rows'][0]['elements'][0]['distance']['text'] ?? null;

            if ($distance1 !== null) {
                $output['taxRate'] = $storecity->tax_rate;
                $output['html']    .= '
        <div class="col-md-12 mb-3 store1">
            <div class="row">
                <div class="col-md-12"><h6>' . $storecity->location_name . '</h6></div>
                <div class="col-md-12"><p>' . $distance1 . '</p></div>
                <div class="col-md-12">
                    <p>' . $storecity->address . ' ,' . $storecity->address_1 . ' ,' . $storecity->city . ' ,' . $storecity->zip . ' ,' . $storecity->state_name . ' ,' . $storecity->country_name . '</p>
                </div>
            </div>
            <div class="selectTimeItem">
                <div class="row">';
                foreach ($store1 as $data1) {
                    $output['html'] .= '
                <div class="col-md-10">
                    Day - ' . $data1->day . ' , ' . date('h:i A', strtotime($data1->open_time)) . ' - ' . date('h:i A', strtotime($data1->close_time)) . '
                </div>
                <div class="col-md-2">
                    <input type="radio" required name="store_id" value="' . $data1->pk_location_times . '/' . $store->pk_locations . '" value="store"> Select
                </div>';
                }
                $output['html'] .= '</div></div></div>';
            }
        }

        return response()->json($output);
    }

    public function otherCheckoutShipInfo(Request $request)
    {
        $store  = Location::where('pk_account', 2)->latest()->first();
        $output = [];

        $output2 = ['html' => null];

        $output1 = [
            'storename' => null,
            'kilomiter' => null,
            'storeName' => null,
            'taxRate'   => null
        ];

        // Get the tax rate
        $getDes  = urlencode("{$store->address}, {$store->city}");
        $getDes1 = urlencode("{$request->address}, {$request->city}");

        $shippingurl = "https://maps.googleapis.com/maps/api/distancematrix/json";
        $params      = [
            'destinations' => $getDes1,
            'origins'      => $getDes,
            'key'          => 'AIzaSyAB80hPTftX9xYXqy6_NcooDtW53kiIH3A',
            'units'        => 'imperial'
        ];

        $client    = new Client();
        $response  = $client->get($shippingurl, ['query' => $params])->getBody();
        $responses = json_decode($response, true);
        $distance  = isset($responses['rows'][0]['elements'][0]['distance']) ?
            $responses['rows'][0]['elements'][0]['distance']['text'] : null;

        if ($distance !== null) {
            $output2['html']      .= $distance;
            $output1['kilomiter'] .= "{$distance}a{$store->pk_locations}b";
        }
        // End of get the tax rate

        // Get the store IDS
        $aa1               = str_replace("m", "", str_replace("b", ",", str_replace("a", ":", str_replace(" ", "", str_replace(",", "", str_replace("mi", "", implode(" ", $output1)))))));
        $convert_to_array1 = explode(",", $aa1);
        $linksArray1       = array_filter($convert_to_array1);
        $tttt1             = array_unique($linksArray1);

        $tttt2 = str_replace(".", "", $tttt1);
        sort($tttt2);
        $str1 = preg_replace("/[^a-zA-Z 0-9]+/", ",", $tttt2);

        sort($str1);
        $str12      = preg_replace("/[^a-zA-Z 0-9]+/", ",", $str1);
        $result1    = array_filter($str12);
        $locationId = explode(",", $result1[0]);

        $storeName                         = DB::table('kbt_locations')->where('pk_locations', $locationId[1])->first();
        $output['storeName']               = $storeName->location_name;
        $output['storeCity']               = $storeName->city;
        $output['taxRate']                 = $storeName->tax_rate;
        $output['pk_location']             = $storeName->pk_locations;
        $estimatedDT                       = Carbon::now()->addDays($storeName->Estimated_Delivery_Time);
        $output['estimated_delivery_time'] = date("d-M-Y", strtotime($estimatedDT));

        $aa = explode(" ", str_replace(",", "", str_replace("mi", "", implode(" ", $output2))));

        $linksArray = array_filter($aa);

        $tttt = array_unique($linksArray);
        sort($tttt);
        $result = array_filter($tttt);
        // End of get the store IDS

        // Get delivery cost
        $deliveryCharge = DB::table('kbt_delivery_charges')
            ->where('miles_from', '<', $result[0])
            ->where('miles_to', '>', $result[0])
            ->latest()->first();

        $output['delivery_charge'] = !$deliveryCharge ? 0 : $deliveryCharge->cost;

        return response()->json($output);
    }

    public function apply_coupon(Request $request)
    {
        $coupon       = DB::table('kbt_coupons')->where('code', $request->couponName)->first();
        $couponAmount = "";
        // foreach($coupon as $data)
        // {
        if (!empty($coupon)) {
            $type         = $coupon->type;
            $couponAmount = $coupon->discount_amount;
        }
        // }
        return response()->json([$couponAmount, $type]);
    }

    public function thank_you($pk_orders = null)
    {
        $order = Order::with(['orderStatus', 'deliveryOption'])->find($pk_orders);

        $locationTime = LocationTime::where('pk_location_times', $order->pk_location_times)->first();

        if (isset($locationTime->pk_locations)) {
            $store = Location::where('pk_locations', $locationTime->pk_locations)->with('locationTime')->first();
        } else {
            $store = Location::where('pk_locations', $order->pk_locations)->with('locationTime')->first();
        }

        $account = "";
        if ($order->choise_details == 'store') {
            $account = Account::where('pk_account', $order->pk_account)
                ->with(['locationType', 'locationType.locationTime'])->first();
        }

        $order_items = $order->order_items;

        $page = view('thank-you-guest', compact(
            'order_items',
            'pk_orders',
            'order',
            'store',
            'account',
            'locationTime'
        ));

        if (auth()->check()) {
            $user_data = auth()->user();
            $page      = view('thank-you', compact(
                'user_data',
                'order_items',
                'pk_orders',
                'order',
                'store',
                'account',
                'locationTime'
            ));
        }
        return $page;
    }

    private function getCartTotal()
    {
        $total    = 0;
        $oth_cart = session()->get('oth_cart');

        foreach ($oth_cart as $id => $details) {
            $total += $details['price'] * $details['quantity'];
        }

        return number_format($total, 2);
    }

    public function getAddressId(Request $request)
    {
        $kbt_address = CustomerAddres::find($request->id);

        if (empty($kbt_address)) {
            $kbt_address = false;
        }

        return json_encode($kbt_address);
    }

    // Create account for other checkout
    public function otherCheckoutCreateAccount(Request $request)
    {
        $request->validate([
            'pk_orders' => 'required|integer',
            'username'  => 'required|string|unique:users',
            'password'  => 'required|min:6|confirmed',
        ]);

        $order = Order::with(['customer'])->find($request->pk_orders);

        // explode customer name
        $name = explode(' ', $order->customer->name);

        // if count of name is 1 then set first name and last name
        if (count($name) == 1) {
            $first_name = $name[0];
            $last_name  = '';
        } else {
            $first_name = $name[0];
            $last_name  = $name[1];
        }


        // Create User for customer
        $user = User::create([
            'first_name'   => $first_name,
            'last_name'    => $last_name,
            'email'        => $request->email,
            'phone'        => $request->office_phone,
            'username'     => $request->username,
            'password'     => Hash::make($request->password),
            'pk_roles'     => 4,
            'pk_account'   => 2,
            'pk_customers' => $order->pk_customers,
        ]);


        // Update customer data
        $order->customer->update([
            'login_enable' => 1,
            'created_by'   => $user->pk_users,
            'updated_by'   => $user->pk_users,
        ]);

        // Update order data
        $order->update([
            'pk_users'   => $user->pk_users,
            'created_by' => $user->pk_users,
            'updated_by' => $user->pk_users,
        ]);

        // Login user
        Auth::loginUsingId($user->pk_users);


        return redirect('/customer');
    }
}
