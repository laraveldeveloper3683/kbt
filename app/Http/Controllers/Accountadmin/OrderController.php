<?php

namespace App\Http\Controllers\Accountadmin;

use App\ArrangementType;
use App\Customer;
use App\CustomerAddres;
use App\FloralArrangement;
use App\Helper\Helper;
use App\Http\Requests\OrderRequest;
use App\Location;
use App\LocationTime;
use App\ProductCategory;
use App\Services\OtherCheckoutService;
use App\Transaction;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Order;
use App\Sale;
use App\OrderItem;
use App\OrderStatus;
use DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use net\authorize\api\contract\v1 as AnetAPI;
use net\authorize\api\controller as AnetController;
use net\authorize\api\constants\ANetEnvironment;

class OrderController extends Controller
{
    public $otherCheckoutService;

    public function __construct()
    {
        $this->otherCheckoutService = new OtherCheckoutService();
    }

    public function index()
    {
        $order_status = OrderStatus::all();
        $orders = Order::latest()->with([
            'orderStatus',
            'customer',
            'location',
        ])->get();

        return view('accountadmin.orders.index', compact('orders', 'order_status'));
    }

    public function create()
    {
        session()->forget('order_cart');
        session()->forget('order_cart_total_quantity');
        $products = DB::table('kbt_floral_arrangements')
            ->join('kbt_floral_arrangements_images', 'kbt_floral_arrangements.pk_floral_arrangements', 'kbt_floral_arrangements_images.pk_floral_arrangements')
            ->select("kbt_floral_arrangements.*", "kbt_floral_arrangements_images.path")
            ->groupBy('kbt_floral_arrangements.pk_floral_arrangements')
            ->get();
        $categories = ProductCategory::all();
        return view('accountadmin.orders.add', compact('products', 'categories'));
    }

    public function productByCategory(Request $request)
    {
        $category = ProductCategory::find($request->category_id);

        if (!$category) {
            $products = DB::table('kbt_floral_arrangements')
                ->join('kbt_floral_arrangements_images', 'kbt_floral_arrangements.pk_floral_arrangements', 'kbt_floral_arrangements_images.pk_floral_arrangements')
                ->select("kbt_floral_arrangements.*", "kbt_floral_arrangements_images.path")
                ->groupBy('kbt_floral_arrangements.pk_floral_arrangements')
                ->get();
            $htmlResponse = view('accountadmin.orders.floral-arrangement-category-response', compact('products'))->render();

            return response()->json([
                'success' => true,
                'data' => $htmlResponse
            ]);
        }

        $products = DB::table('kbt_floral_arrangements')
            ->join('kbt_floral_arrangements_images', 'kbt_floral_arrangements.pk_floral_arrangements', 'kbt_floral_arrangements_images.pk_floral_arrangements')
            ->select("kbt_floral_arrangements.*", "kbt_floral_arrangements_images.path")
            ->groupBy('kbt_floral_arrangements.pk_floral_arrangements')
            ->where('kbt_floral_arrangements.pk_product_category', $category->pk_product_category)
            ->get();
        $htmlResponse = view('accountadmin.orders.floral-arrangement-category-response', compact('products'))->render();

        return response()->json([
            'success' => true,
            'data' => $htmlResponse
        ]);
    }

    public function addToCart(Request $request)
    {
        $flower = DB::table('kbt_floral_arrangements')
            ->join('kbt_floral_arrangements_images', 'kbt_floral_arrangements.pk_floral_arrangements', 'kbt_floral_arrangements_images.pk_floral_arrangements')
            ->where('kbt_floral_arrangements.pk_floral_arrangements', $request->id)
            ->select("kbt_floral_arrangements.*", "kbt_floral_arrangements_images.path")
            ->groupBy('kbt_floral_arrangements.pk_floral_arrangements')
            ->first();

        $arrangementTypes = ArrangementType::leftjoin('kbt_floral_arrangement_prices', 'kbt_arrangement_type.pk_arrangement_type', '=', 'kbt_floral_arrangement_prices.pk_arrangement_type')
            ->select('kbt_arrangement_type.pk_arrangement_type', 'kbt_arrangement_type.arrangement_type', 'kbt_floral_arrangement_prices.price')
            ->where('kbt_floral_arrangement_prices.pk_arrangement_type', $request->type)
            ->where('kbt_floral_arrangement_prices.pk_floral_arrangements', $request->id)
            ->where('kbt_arrangement_type.pk_account', $flower->pk_account)
            ->first();
        $flower_name = FloralArrangement::find($request->id);

        $flower_name = !empty($flower_name) ? $flower_name->title : '';


        $arrangementTypesName = !empty($arrangementTypes) ? $arrangementTypes->arrangement_type : '';
        $pk_arrangement_type = !empty($request->arrangementType) ? $request->arrangementType : '';

        $flower_bouquet_data = join(' - ', array_filter([$flower_name, $arrangementTypesName]));
        $flower_description = !empty($flower->description) ? $flower->description : '';

        $quantity = !empty($request->quantity) ? $request->quantity : 1;
        $price = $flower ? (!empty($flower->price) ? $flower->price : 0) : '0.00';
        $photo = !empty($flower->path) ? $flower->path : '';
        $orderCart = session()->get('order_cart', []);

        $orderTotalQty = session()->get('order_cart_total_quantity', 0);

        if (!empty($orderCart[$flower->pk_floral_arrangements])) {
            $orderCart[$flower->pk_floral_arrangements]['quantity']++;
            $orderTotalQty += 1;
        } else {
            $orderCart[$flower->pk_floral_arrangements] = [
                "name" => $flower_bouquet_data,
                "arrangementTypesName" => $arrangementTypesName,
                "pk_arrangement_type" => $pk_arrangement_type,
                "description" => $flower_description,
                "quantity" => $quantity,
                "price" => $price,
                "photo" => $photo,
                "type" => 5
            ];
            $orderTotalQty += 1;
        }

        session()->put('order_cart', $orderCart);
        session()->put('order_cart_total_quantity', $orderTotalQty);

        return response()->json([
            'msg' => 'Product added to cart successfully!',
            'totalQty' => $orderTotalQty
        ]);
    }

    public function updateCartItem(Request $request)
    {
        $id = $request->id;
        $quantity = $request->quantity;
        $cart = session('order_cart');

        $cart[$id]["quantity"] = $quantity;

        session()->put('order_cart', $cart);

        $total = $quantity * $request->price;


        session()->put('order_cart_total_quantity', array_sum(array_column($cart, 'quantity')));

        return response()->json([
            'msg' => 'Cart updated successfully.',
            'total' => $total,
            'totalQty' => $quantity
        ]);
    }

    public function checkout()
    {
        if (!session('order_cart') || !count(session('order_cart'))) {
            return redirect()->route('accountadmin.orders.create')
                ->with('error', 'The cart is empty, please add some products to cart!');
        }

        $cartItems = (array)session('order_cart');
        $user_data = auth()->user();

        $location = $this->getLocation();

        return view('accountadmin.orders.checkout', compact('user_data', 'location', 'cartItems'));
    }

    public function placeOrder(OrderRequest $request)
    {
        try {
            DB::beginTransaction();

            if ($request->has('is_order_sale')) {
                if (!$request->pk_order) {
                    return redirect()->back()
                        ->with('error', 'Please select an order!');
                }

                $order = Order::with('order_items')->find($request->pk_order);

                if ($order) {
                    $this->saveOrderSaleData($order);
                }

                DB::commit();

                return redirect()->route('accountadmin.sales.index')
                    ->with('success', 'Sale created successfully.');
            }

            $orderCart = session('order_cart');

            if (!$orderCart || !count($orderCart)) {
                return redirect()->back()->with('error', 'The cart is empty, please add some products to cart!');
            }

            $customer = Customer::find($request->pk_customer);

            if (!$customer) {
                return redirect()->back()->with('error', 'Customer not found!');
            }

            $user = User::where('email', $customer->email)->first();
            if (!$user) {
                return redirect()->back()->with('error', 'Customer not found!');
            }
            $pk_user = $user->pk_users;

            $cartTotal = $this->getCartTotal();

            $location = Location::where('pk_locations', $request->pk_locations)->first();

            $grandTotal = $cartTotal;
            if ($location) {
                $taxAmount = ($grandTotal * $location->tax_rate) / 100;
                $grandTotal = $grandTotal + $taxAmount;
            }

            $res = $this->handlePaymentByGateway($request, $grandTotal);
            if ($res['msg_type'] == 'error_msg') {
                session()->flash('error', $res['message_text']);
                return back()->withInput();
            }

            $orderData = [
                'pk_account' => 1,
                'pk_users' => $pk_user,
                'pk_transactions' => $res['trans_id'] ?? null,
                'pk_customers' => $request->pk_customer,
                'total' => $grandTotal,
                'pk_locations' => $location->pk_locations ?? null,
                'pk_location_times' => LocationTime::where('pk_locations', $location->pk_locations)
                        ->first()->pk_location_times ?? null,
                'pk_order_status' => 1,
                'tax_charge' => $location->tax_rate ?? 0,
                'subtotal' => $cartTotal,
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ];

            $order = Order::create($orderData);
            foreach ($orderCart as $item) {
                $order->order_items()->create([
                    'pk_shipping_address' => 1,
                    'pk_arrangement_type' => $item['pk_arrangement_type'] ?? 1,
                    'name' => $item['name'],
                    'description' => $item['description'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'card_message' => '',
                    'created_by' => Auth::id(),
                    'updated_by' => Auth::id(),
                ]);
            }

            DB::commit();

            session()->forget('order_cart');
            session()->forget('order_cart_total_quantity');

            return redirect()->route('accountadmin.orders.index')
                ->with('success', 'Order created successfully.');
        } catch (ValidationException $exception) {
            DB::rollBack();
            throw $exception;
        } catch (\Exception $exception) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Something went wrong, please try again later!');
        }
    }

    private function handlePaymentByGateway($request, $total_amount)
    {
        $qty = $this->getCartTotalQuantity();
        $order_no = 'SALE' . str_pad(Sale::max('pk_sales') + 1, 8, "0", STR_PAD_LEFT);

        // Card details
        $cardName = $request->cc_name;
        $cardNumber = preg_replace('/\s+/', '', $request->cc_number);
        $expiryYear = $request->expiry_year;
        $expiryMonth = $request->expiry_month;
        $cvv = $request->cvv;
        $expiryDate = $expiryYear . '-' . $expiryMonth;


        // Customer data
        $customer = Customer::where('pk_customers', $request->pk_customer)->first();


        // Set up merchant authentication
        $merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
        $merchantAuthentication->setName('4Y5pCy8Qr');
        $merchantAuthentication->setTransactionKey('4ke43FW8z3287HV5');

        $refId = 'ref' . time();

        // Create credit card object
        $creditCard = new AnetAPI\CreditCardType();
        $creditCard->setCardNumber($cardNumber);
        $creditCard->setExpirationDate($expiryDate);
        $creditCard->setCardCode($cvv);

        // Create payment object
        $paymentOne = new AnetAPI\PaymentType();
        $paymentOne->setCreditCard($creditCard);

        // Create order information
        $order = new AnetAPI\OrderType();
        $order->setInvoiceNumber($order_no);
        $order->setDescription('KBT Sale');


        if ($customer) {
            // Set customer's address
            $customerAddress = new AnetAPI\CustomerAddressType();
            $customerAddress->setFirstName($cardName);
            $customerAddress->setLastName($cardName);
            $customerAddress->setAddress($customer->address[0]->address ?? '');
            $customerAddress->setCity($customer->address[0]->city ?? '');
            $customerAddress->setState($customer->address[0]->state->state_name ?? '');
            $customerAddress->setZip($customer->address[0]->zip ?? '');
            $customerAddress->setCountry($customer->address[0]->country->country_code ?? 'USA');

            // Set customer's data
            $customerData = new AnetAPI\CustomerDataType();
            $customerData->setEmail($customer->email ?? '');
        }


        // Create transaction request
        $transactionRequestType = new AnetAPI\TransactionRequestType();
        $transactionRequestType->setTransactionType("authCaptureTransaction");
        $transactionRequestType->setAmount($total_amount);
        $transactionRequestType->setOrder($order);
        $transactionRequestType->setPayment($paymentOne);
        if ($customer) {
            $transactionRequestType->setBillTo($customerAddress);
        }

        // Assemble the complete transaction request
        $requests = new AnetAPI\CreateTransactionRequest();
        $requests->setMerchantAuthentication($merchantAuthentication);
        $requests->setRefId($refId);
        $requests->setTransactionRequest($transactionRequestType);

        // Create the transaction controller and get the response
        $controller = new AnetController\CreateTransactionController($requests);
        $response = $controller->executeWithApiResponse(ANetEnvironment::SANDBOX);

        $trans_id = 0;

        // Check if the API request was successful
        if ($response == null) {
            return ['msg_type' => "error_msg", 'message_text' => "No response returned", 'trans_id' => $trans_id];
        }

        if ($response->getMessages()->getResultCode() == "Ok") {
            // Parse the transaction response
            $tresponse = $response->getTransactionResponse();

            if ($tresponse != null && $tresponse->getMessages() != null) {
                $message_text = $tresponse->getMessages()[0]->getDescription() . ", Transaction ID: " . $tresponse->getTransId();
                $msg_type = "success_msg";

                // Create a new transaction record
                $trans_id = Transaction::create([
                    'amount' => $total_amount,
                    'response_code' => $tresponse->getResponseCode(),
                    'transaction_id' => $tresponse->getTransId(),
                    'auth_id' => $tresponse->getAuthCode(),
                    'message_code' => $tresponse->getMessages()[0]->getCode(),
                    'name_on_card' => trim($cardName),
                    'account_type' => $tresponse->getAccountType(),
                    'currency' => 'USD',
                    'created_by' => auth()->user()->pk_users,
                    'quantity' => $qty
                ])->pk_transactions;
            } else {
                $message_text = 'There were some issues with the payment. Please try again later.';
                $msg_type = "error_msg";

                if ($tresponse->getErrors() != null) {
                    $message_text = $tresponse->getErrors()[0]->getErrorText();
                    $msg_type = "error_msg";
                }
            }
        } else {
            $message_text = 'There were some issues with the payment. Please try again later!';
            $msg_type = "error_msg";

            /*$tresponse = $response->getTransactionResponse();

            if ($tresponse != null && $tresponse->getErrors() != null) {
                $message_text = $tresponse->getErrors()[0]->getErrorText();
                $msg_type     = "error_msg";
            } else {
                $message_text = $response->getMessages()->getMessage()[0]->getText();
                $msg_type     = "error_msg";
            }*/
        }

        return ['msg_type' => $msg_type, 'message_text' => $message_text, 'trans_id' => $trans_id];
    }

    public function storeSaleFromOrder(Request $request)
    {
        try {
            DB::beginTransaction();

            if (!$request->pk_order) {
                return redirect()->back()
                    ->with('error', 'Please select an order!');
            }

            $order = Order::with(['order_items', 'customer'])->find($request->pk_order);

            if ($order) {
                $this->saveOrderSaleData($order);
            }


            DB::commit();

            session()->forget('pos_cart');
            session()->forget('pos_total_quantity');

            return redirect()->route('accountadmin.sales.index')
                ->with('success', 'Sale created successfully.');
        } catch (\Exception $exception) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Something went wrong, please try again later!' . $exception->getMessage());
        }
    }

    private function saveOrderSaleData(Order $order)
    {
        $customer = Customer::where('email', $order->email)->first();
        $orderTotal = $this->getOrderTotal($order);
        $taxRate = $order->location->tax_rate ?? 0;
        $taxAmount = ($orderTotal * $taxRate) / 100;
        $grandTotal = $orderTotal + $taxAmount;

        $newOrder = Order::create([
            'pk_orders' => $order->pk_orders,
            'pk_users' => $order->pk_users,
            'pk_account' => 1,
            'pk_order_status' => 3,
            'pk_arrangement_type' => $order->pk_arrangement_type,
            'customer_name' => $order->customer->customer_name ?? 'Store/Cash Sale',
            'pk_customers' => $customer->pk_customers ?? null,
            'subtotal' => $order->total,
            'tax_total' => $taxRate,
            'total' => $grandTotal,
            'pk_sales_type' => $saleType->pk_sales_type ?? null,
            'pk_transactions' => $order->pk_transactions,
            'pk_locations' => $order->pk_locations,
            'pk_location_times' => $order->pk_location_times,
            'is_paid' => (bool)$order->pk_transactions,
            'discountCharge' => $order->discountCharge,
            'coupon_discount_type' => $order->coupon_discount_type,
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ]);

        $order = Order::find($order->pk_orders);
        $order->pk_order_status = 3;
        $order->save();

        if ($order->order_items->count()) {
            foreach ($order->order_items as $item) {
                $sale->saleItems()->create([
                    'name' => $item['name'],
                    'description' => $item['description'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'type' => $item['type'],
                    'created_by' => Auth::id(),
                    'updated_by' => Auth::id(),
                ]);
            }
        }

        return $sale;
    }

    private function getLocation()
    {
        $storeAddress = '1838 Newport Boulevard';
        $storeCity = 'Costa Mesa';
        $zip = '92627';
        $locationName = 'Costa Mesa';
        return Location::where('location_name', $locationName)
            ->orWhere('address', $storeAddress)
            ->orWhere('city', $storeCity)
            ->orWhere('zip', $zip)
            ->first();
    }

    // Get cart total
    private function getCartTotal()
    {
        $total = 0;

        $cart = session()->get('order_cart');

        foreach ($cart as $details) {
            $total += $details['price'] * $details['quantity'];
        }

        return number_format($total, 2);
    }

    private function getOrderTotal(Order $order)
    {
        $total = 0;

        if ($order->order_items->count()) {
            foreach ($order->order_items as $item) {
                $total += $item->price * $item->quantity;
            }
        }

        return $total;
    }

    // Get quantity total
    private function getCartTotalQuantity()
    {
        $total = 0;

        $cart = session()->get('order_cart');

        foreach ($cart as $details) {
            $total += $details['quantity'];
        }

        return $total;
    }

    public function detail($id)
    {
        $orders = Order::with(['transactions', 'orderStatus', 'customer'])->where('pk_account', 2)
            ->where("pk_orders", $id)->first();
        $items = OrderItem::where('pk_orders', $id)->get();
        $orderStatus = OrderStatus::all();
        $customerAddress = CustomerAddres::where('pk_customers', $orders->pk_customers)->latest()
            ->with(['state', 'country'])->first();

        $account = null;
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
                'cancel_reason' => $request->cancel_reason,
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
            'order_status' => $order_status]);
    }

    public function cancelOrder(Request $request, $id = null)
    {
        $order = Order::find($id);
        $order->pk_order_status = 5;
        $order->save();
        return redirect()->back()->with(Helper::getAcknowledge('ORDER_STATUS_UPDATE'));
    }

}
