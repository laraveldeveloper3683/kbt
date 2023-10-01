<?php

namespace App\Services;

use App\Addres;
use App\Customer;
use App\CustomerAddres;
use App\DeliveryOrPickup;
use App\Order;
use App\OrderItem;
use App\State;
use App\Transaction;
use App\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use net\authorize\api\contract\v1 as AnetAPI;
use net\authorize\api\controller as AnetController;

class OtherCheckoutService
{
    /**
     * Other checkout check for guest
     *
     * @throws Exception
     */
    public function otherCheckoutForGuest(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'first_name'           => 'required',
                'last_name'            => 'required',
                'phone'                => 'required',
                'email'                => 'nullable|unique:users',
                'billing_address'      => 'required',
                'billing_city'         => 'required',
                'billing_state_name'   => 'required',
                'billing_zip'          => 'required',
                'billing_country_name' => 'required',
            ]);

            if ($validator->fails()) {
                throw ValidationException::withMessages($validator->errors()->toArray());
            }


            // Create Customer
            $customer = Customer::create([
                'pk_account'       => 2,
                'customer_name'    => $request->first_name . ' ' . $request->last_name,
                'pk_customer_type' => 1,
                'email'            => $request->email,
                'office_phone'     => $request->phone,
                'login_enable'     => 0,
            ]);

            // Get customer and user id
            $pk_customer_id = $customer->pk_customers;

            // Create Customer Address
            if ($request->billing_address) {
                $billingState   = State::where('state_code', $request->billing_state_name)->first();
                $billing_data   = [
                    'pk_customers'    => $pk_customer_id,
                    'pk_address_type' => 2,
                    'address'         => $request->billing_address,
                    'address_1'       => $request->billing_address_1,
                    'city'            => $request->billing_city,
                    'pk_states'       => $billingState->pk_states ?? 1,
                    'pk_country'      => $billingState->pk_country ?? 1,
                    'zip'             => $request->billing_zip,
                ];
                $billingAddress = CustomerAddres::create($billing_data);
            }

            return [
                $customer,
                $pk_customer_id,
                $primaryAddress ?? null,
                $billingAddress ?? null,
                $validator
            ];
        } catch (Exception $e) {
            throw $e;
        }
    }

    // Other checkout store for guest
    public function otherCheckoutGuestStore(Request $request, $pk_customer_id)
    {
        try {
            $customer_data = Customer::find($pk_customer_id);

            $cartItems = session('oth_cart');

            // Check customer exists or not
            if (!$customer_data) {
                throw ValidationException::withMessages(['error' => 'Customer not found!']);
            }

            // Process payment
            $pk_transactions = null;
            $payment_total   = 0;
            $payem_total_qty = 0;

            if (session('oth_cart')) {
                foreach ((array)session('oth_cart') as $orderitempay) {
                    $quantity_payment = !empty($orderitempay['quantity']) ? $orderitempay['quantity'] : 0;
                    if ($quantity_payment > 0) {
                        $payment_total   += $orderitempay['price'] * $quantity_payment;
                        $payem_total_qty += $quantity_payment;
                    }
                }

                if (!empty($customer_data->email)) {
                    $user_email = $customer_data->email;

                    $request->request->add(['billing_email' => $user_email]);
                }

                $order_no = 'ORD' . str_pad(Order::max('pk_orders') + 1, 8, "0", STR_PAD_LEFT);
                $res      = $this->handleonlineguestpay($request, $payment_total, $payem_total_qty, $order_no, $customer_data);

                if ($res['msg_type'] == 'error_msg') {
                    // New validation exception for payment error
                    throw ValidationException::withMessages(['error' => $res['message_text'] . ', please correct errors!']);
                }

                $pk_transactions = $res['trans_id'];
            }

            // Delivery option
            $deliveryOption = DeliveryOrPickup::where('pk_delivery_or_pickup', @$request->choise_details)->first();

            // Save order
            $save_order = [
                'pk_transactions'       => $pk_transactions,
                'pk_customers'          => $pk_customer_id,
                'pk_delivery_or_pickup' => $request->choise_details ?? 1,
                'subtotal'              => $payment_total,
                'total'                 => $request->amount,
            ];

            if ($request->pk_locations) {
                $save_order['pk_locations'] = $request->pk_locations;
            }

            if ($request->pk_location_times) {
                $save_order['pk_location_times'] = $request->pk_location_times;
            }

            if ($request->store_id) {
                $save_order['pk_locations'] = $request->store_id;
            }

            if ($request->store_time_id) {
                $save_order['pk_location_times'] = $request->store_time_id;
            }

            if (isset($request->deleveryCast1)) {
                $save_order['delivery_charge'] = $request->deleveryCast1;
            }


            if (isset($request->shippingCharge)) {
                $save_order['tax_charge'] = $request->shippingCharge;
            }

            if (isset($request->discountCharge)) {
                $coupon = explode(" ", $request->discountCharge);

                if ($coupon[1] === '%') {
                    $save_order['discount_charge']      = $coupon[0];
                    $save_order['coupon_discount_type'] = 'percent';
                } elseif ($coupon[0] === '$') {
                    $save_order['discount_charge']      = $coupon[1];
                    $save_order['coupon_discount_type'] = 'fixed';
                } else {
                    $save_order['discount_charge']      = $request->discountCharge[0];
                    $save_order['coupon_discount_type'] = $request->discountCharge[1];
                }
            }

            if (isset($request->estimated_del)) {
                $save_order['estimated_del'] = Carbon::parse($request->estimated_del)->format('Y-m-d');
            }

            $save_order['pk_order_status'] = 1;

            if ($deliveryOption->delivery_or_pickup == 'Store Pickup') {
                $save_order['pickup_date']     = Carbon::parse($request->pickup_date)->format('Y-m-d');
                $save_order['delivery_charge'] = null;
                $save_order['estimated_del']   = null;
                $save_order['delivery_date']   = null;
            }

            if ($cartItems && count($cartItems) > 0 && count($cartItems) == 1) {
                // Get first key
                $firstKey                      = array_key_first($cartItems);
                $save_order['delivery_charge'] = $cartItems[$firstKey]['delivery_charge'] ?? null;
                $save_order['estimated_del']   = $cartItems[$firstKey]['estimated_del'] ?? null;
                $save_order['delivery_date']   = isset($cartItems[$firstKey]['delivery_date']) ?
                    Carbon::parse($cartItems[$firstKey]['delivery_date'])->format('Y-m-d') : null;
            }

            // Create order
            $get_order = Order::create($save_order);

            if ($cartItems && count($cartItems) > 0) {
                $total           = 0;
                $deliveryCharges = 0;
                // $sameAsBilling   = 1;
                $duplicateAddresses = [];

                foreach ($cartItems as $key => $orderitem) {
                    $quantity = $orderitem['quantity'] ?? 0;

                    if ($quantity > 0) {
                        $total += $orderitem['price'] * $quantity;

                        $save_order_item = [
                            'pk_orders'           => $get_order->pk_orders,
                            'pk_shipping_address' => 1,
                            'pk_arrangement_type' => $orderitem['pk_arrangement_type'] ?? '',
                            'name'                => $orderitem['name'] ?? '',
                            'description'         => $orderitem['description'] ?? '',
                            'quantity'            => $quantity,
                            'price'               => $orderitem['price'] ?? 0,
                            'card_message'        => $orderitem['card_message'] ?? '',
                        ];

                        // Create order item
                        $orderItem = OrderItem::create($save_order_item);

                        // Create item address
                        if ($deliveryOption->delivery_or_pickup == 'Delivery') {
                            $user_name  = $request->first_name . ' ' . $request->last_name;
                            $user_email = $request->email;
                            $user_phone = $request->phone;

                            if (isset($request->item_address) && count($request->item_address)) {
                                $itemAddress = $request->item_address;
                                $itemAddr    = $itemAddress[$key] ?? null;
                                $address     = $itemAddr['shipping_address'] . ' ' . $itemAddr['shipping_address_1'] . ' ' .
                                    $itemAddr['shipping_city'] . ' ' . $itemAddr['shipping_state_name'] . ' ' .
                                    $itemAddr['shipping_zip'] . ' ' . $itemAddr['delivery_date'];

                                if ($itemAddress[$key]['same_as_billing'] == 0 && !in_array($address, $duplicateAddresses)) {
                                    $deliveryCharges += $itemAddress[$key]['delivery_charge'];
                                } else {
                                    $itemAddress[$key]['same_as_billing'] = 1;
                                }

                                // Shipping address create for order items
                                $cusAddr            = @$customer_data->address[0];
                                $shipping_address   = $itemAddress[$key]['shipping_address'] ?? $request->billing_address ??
                                    $cusAddr->address ?? null;
                                $shipping_address_1 = $itemAddress[$key]['shipping_address_1'] ?? $request->billing_address_1 ??
                                    $cusAddr->address_1 ?? null;
                                $shipping_city      = $itemAddress[$key]['shipping_city'] ?? $request->billing_city
                                    ?? $cusAddr->city ?? null;
                                $shipping_zip       = $itemAddress[$key]['shipping_zip'] ?? $request->billing_zip
                                    ?? $cusAddr->zip ?? null;
                                $state              = State::where('state_code', $itemAddress[$key]['shipping_state_name']
                                    ?? $request->billing_state_name ?? null)->first();
                                $delivery_date      = $itemAddress[$key]['delivery_date'] ?? $request->delivery_date ?? null;
                                $shipping_data      = [
                                    'pk_customers'       => $pk_customer_id,
                                    'pk_order_items'     => $orderItem->pk_order_items ?? 1,
                                    'shipping_full_name' => $itemAddress[$key]['shipping_full_name'] ?? $user_name,
                                    'shipping_email'     => $itemAddress[$key]['shipping_email'] ?? $user_email,
                                    'shipping_phone'     => $itemAddress[$key]['shipping_phone'] ?? $user_phone,
                                    'shipping_address'   => $shipping_address,
                                    'shipping_address_1' => $shipping_address_1,
                                    'shipping_city'      => $shipping_city,
                                    'pk_states'          => $state->pk_states ?? $cusAddr->pk_states ?? 1,
                                    'pk_country'         => $state->pk_country ?? $cusAddr->pk_country ?? 1,
                                    'shipping_zip'       => $shipping_zip,
                                    'delivery_charge'    => $itemAddress[$key]['delivery_charge'] ?? 0,
                                    'same_as_billing'    => $itemAddress[$key]['same_as_billing'] ?? 1,
                                    'delivery_date'      => Carbon::parse($delivery_date)->format('Y-m-d'),
                                ];

                                // Create shipping address
                                if ($shipping_address) {
                                    Addres::create($shipping_data);

                                    // Create customer address
                                    CustomerAddres::updateOrCreate([
                                        'pk_customers'    => $pk_customer_id,
                                        'pk_address_type' => 0,
                                        'address'         => $shipping_address,
                                        'city'            => $shipping_city,
                                        'zip'             => $shipping_zip,
                                        'pk_states'       => $state->pk_states ?? $cusAddr->pk_states ?? 1,
                                    ], [
                                        'address_1'  => $shipping_address_1,
                                        'pk_country' => $state->pk_country ?? $cusAddr->pk_country ?? 1,
                                        'lat'        => $itemAddress[$key]['shipping_lat'] ?? null,
                                        'lng'        => $itemAddress[$key]['shipping_lng'] ?? null,
                                    ]);
                                }

                                $duplicateAddresses[$key] = $address;
                            }
                        }
                    }
                }

                // Calculate discount from coupon discount
                $save_order['discountCharge'] = 0;
                if (isset($request->discountCharge)) {
                    if ($save_order['coupon_discount_type'] == 'percent') {
                        $save_order['discountCharge'] = ($total * $save_order['discount_charge']) / 100;
                    } else {
                        $save_order['discountCharge'] = $save_order['discount_charge'];
                    }
                }

                if ($deliveryOption->delivery_or_pickup == 'Delivery') {
                    $deliveryCharges = $deliveryCharges > 0 ? $deliveryCharges : $request->deleveryCast1;
                    $taxTotal        = ($total * $request->shippingCharge) / 100;
                    $total           += ($deliveryCharges + $taxTotal) - $save_order['discountCharge'];
                    $get_order->update([
                        'total'           => $total,
                        'delivery_charge' => $deliveryCharges
                    ]);
                } else {
                    $taxTotal = ($total * $request->shippingCharge) / 100;
                    $total    += $taxTotal - $save_order['discountCharge'];
                    $get_order->update([
                        'total'           => $total,
                        'delivery_charge' => null,
                    ]);
                }
            }

            return [
                $get_order,
                $save_order
            ];
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Handle online payment / Complete transaction for guest
     *
     * @param $request
     * @param $totla_amount
     * @param $qty
     * @param $order_no
     * @param $customer
     * @return array
     */
    private function handleonlineguestpay($request, $totla_amount, $qty, $order_no, $customer)
    {
        $input = $request->input();

        // Get billing address
        $billing_address    = $request->billing_address ?? '';
        $billing_city       = $request->billing_city ?? '';
        $billing_state_name = $request->billing_state_name ?? '';
        $billing_zip        = $request->billing_zip ?? '';
        $billing_email      = $request->billing_email ?? '';

        /* Create a merchantAuthenticationType object with authentication details
          retrieved from the constants file */
        $merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
        //$merchantAuthentication->setName(env('MERCHANT_LOGIN_ID'));
        $merchantAuthentication->setName('4Y5pCy8Qr');
        //$merchantAuthentication->setTransactionKey(env('MERCHANT_TRANSACTION_KEY'));
        $merchantAuthentication->setTransactionKey('4ke43FW8z3287HV5');

        // Set the transaction's refId
        $refId      = 'ref' . time();
        $cardNumber = preg_replace('/\s+/', '', $input['cc_number']);

        // Create the payment data for a credit card
        $creditCard = new AnetAPI\CreditCardType();
        $creditCard->setCardNumber($cardNumber);
        $creditCard->setExpirationDate($input['expiry_year'] . "-" . $input['expiry_month']);
        $creditCard->setCardCode($input['cvv']);

        // Add the payment data to a paymentType object
        $paymentOne = new AnetAPI\PaymentType();
        $paymentOne->setCreditCard($creditCard);

        // Create order information
        $order = new AnetAPI\OrderType();
        $order->setInvoiceNumber($order_no);
        $order->setDescription('KBT');


        // Set the customer's Bill To address
        $customerAddress = new AnetAPI\CustomerAddressType();
        $customerAddress->setFirstName($input['cc_name']);
        $customerAddress->setLastName($input['cc_name']);

        if ($customer && !isset($input['address'])) {
            $cusAddr             = $customer->address[0] ?? null;
            $input['email']      = $customer->email;
            $input['address']    = $cusAddr->address ?? $billing_address;
            $input['city']       = $cusAddr->city ?? $billing_city;
            $input['state_name'] = $cusAddr->state->state_name ?? $billing_state_name ?? 'CA';
            $input['zip']        = $cusAddr->zip ?? $billing_zip;
        }

        $customerAddress->setAddress($input['address'] ?? $billing_address);
        $customerAddress->setCity($input['city'] ?? $billing_city);
        $customerAddress->setState($input['state_name'] ?? $billing_state_name);
        $customerAddress->setZip($input['zip'] ?? $billing_zip);
        $customerAddress->setCountry('United States');

        // Set the customer's identifying information
        $customerData = new AnetAPI\CustomerDataType();
        $customerData->setEmail($input['email'] ?? $billing_email);

        // Create a TransactionRequestType object and add the previous objects to it
        $transactionRequestType = new AnetAPI\TransactionRequestType();
        $transactionRequestType->setTransactionType("authCaptureTransaction");
        $transactionRequestType->setAmount($totla_amount);
        $transactionRequestType->setOrder($order);
        $transactionRequestType->setPayment($paymentOne);
        $transactionRequestType->setBillTo($customerAddress);

        // Assemble the complete transaction request
        $requests = new AnetAPI\CreateTransactionRequest();
        $requests->setMerchantAuthentication($merchantAuthentication);
        $requests->setRefId($refId);
        $requests->setTransactionRequest($transactionRequestType);

        // Create the controller and get the response
        $controller = new AnetController\CreateTransactionController($requests);
        $response   = $controller->executeWithApiResponse(\net\authorize\api\constants\ANetEnvironment::SANDBOX);
        //$response = $controller->executeWithApiResponse(\net\authorize\api\constants\ANetEnvironment::PRODUCTION);

        $trans_id = 0;
        if ($response != null) {
            // Check to see if the API request was successfully received and acted upon
            if ($response->getMessages()->getResultCode() == "Ok") {
                // Since the API request was successful, look for a transaction response
                // and parse it to display the results of authorizing the card
                $tresponse = $response->getTransactionResponse();

                if ($tresponse != null && $tresponse->getMessages() != null) {
                    //                    echo " Successfully created transaction with Transaction ID: " . $tresponse->getTransId() . "\n";
                    //                    echo " Transaction Response Code: " . $tresponse->getResponseCode() . "\n";
                    //                    echo " Message Code: " . $tresponse->getMessages()[0]->getCode() . "\n";
                    //                    echo " Auth Code: " . $tresponse->getAuthCode() . "\n";
                    //                    echo " Description: " . $tresponse->getMessages()[0]->getDescription() . "\n";
                    $message_text = $tresponse->getMessages()[0]->getDescription() . ", Transaction ID: " . $tresponse->getTransId();
                    $msg_type     = "success_msg";

                    $trans_id = Transaction::create([
                        'amount'         => $totla_amount,
                        'response_code'  => $tresponse->getResponseCode(),
                        'transaction_id' => $tresponse->getTransId(),
                        'auth_id'        => $tresponse->getAuthCode(),
                        'message_code'   => $tresponse->getMessages()[0]->getCode(),
                        'name_on_card'   => trim($input['cc_name']),
                        'account_type'   => $tresponse->getAccountType(),
                        'currency'       => 'USD',
                        'quantity'       => $qty
                    ])->pk_transactions;
                } else {
                    $message_text = 'There were some issue with the payment. Please try again later.';
                    $msg_type     = "error_msg";

                    if ($tresponse->getErrors() != null) {
                        $message_text = $tresponse->getErrors()[0]->getErrorText();
                        $msg_type     = "error_msg";
                    }
                }
                // Or, print errors if the API request wasn't successful
            } else {
                $message_text = 'There were some issue with the payment. Please try again later.';
                $msg_type     = "error_msg";

                $tresponse = $response->getTransactionResponse();

                if ($tresponse != null && $tresponse->getErrors() != null) {
                    $message_text = $tresponse->getErrors()[0]->getErrorText();
                    $msg_type     = "error_msg";
                } else {
                    $message_text = $response->getMessages()->getMessage()[0]->getText();
                    $msg_type     = "error_msg";
                }
            }
        } else {
            $message_text = "No response returned";
            $msg_type     = "error_msg";
        }

        return [
            'msg_type'     => $msg_type,
            'message_text' => $message_text,
            'trans_id'     => $trans_id
        ];
    }


    // Other checkout check for auth
    public function otherCheckoutForAuth(Request $request)
    {
        try {
            $user_data                    = auth()->user();
            $request['billing_full_name'] = $request->first_name ?? $user_data->first_name . ' ' . $user_data->last_name;
            $validator                    = Order::validate_payment_card($request->all());

            if ($validator->fails()) {
                throw ValidationException::withMessages($validator->errors()->toArray());
            }

            $pk_user_id     = $user_data->pk_users;
            $customer_data1 = Customer::where('email', $user_data->email)->first();

            // Check if customer already exists and create if not
            $first_name = $user_data->first_name ?? $request->first_name;
            $last_name  = $user_data->last_name ?? $request->last_name;
            $username   = $user_data->username ?? $request->username;
            $email      = $user_data->email ?? $customer_data1->email ?? $request->email;
            $phone      = $customer_data1->office_phone ?? $user_data->phone;
            if ($customer_data1) {
                $pk_customer_id = $customer_data1->pk_customers;
                $customer_data1->update([
                    'customer_name' => $customer_data1->customer_name ? $customer_data1->customer_name : $first_name . ' ' . $last_name,
                    'email'         => $customer_data1->email ?? $email,
                    'office_phone'  => $customer_data1->office_phone ?? $phone,
                ]);
                $user_data->update([
                    'pk_customers' => $pk_customer_id,
                    'first_name'   => $first_name,
                    'last_name'    => $last_name,
                    'username'     => $username,
                    'email'        => $email,
                    'phone'        => $phone,
                ]);

                // Create customer primary address
                $primaryAddressExists = CustomerAddres::where('pk_customers', $pk_customer_id)
                    ->where('pk_address_type', 1)->first();
                if ($request->primary_address && !$primaryAddressExists) {
                    $primaryState    = State::where('state_code', $request->primary_state_name)->first();
                    $primary_address = [
                        'pk_customers'    => $pk_customer_id,
                        'pk_address_type' => 1,
                        'address'         => $request->primary_address,
                        'address_1'       => $request->primary_address_1,
                        'city'            => $request->primary_city,
                        'pk_states'       => $primaryState->pk_states ?? 1,
                        'pk_country'      => $primaryState->pk_country ?? 1,
                        'zip'             => $request->primary_zip,
                    ];

                    $primaryAddress = CustomerAddres::create($primary_address);
                }

                // Create customer billing address
                $billingAddressExists = CustomerAddres::where('pk_customers', $pk_customer_id)
                    ->where('pk_address_type', 2)->first();
                if ($request->billing_address && !$billingAddressExists) {
                    $billingState   = State::where('state_code', $request->billing_state_name)->first();
                    $billing_data   = [
                        'pk_customers'    => $pk_customer_id,
                        'pk_address_type' => 2,
                        'address'         => $request->billing_address,
                        'address_1'       => $request->billing_address_1,
                        'city'            => $request->billing_city,
                        'pk_states'       => $billingState->pk_states ?? 1,
                        'pk_country'      => $billingState->pk_country ?? 1,
                        'zip'             => $request->billing_zip,
                    ];
                    $billingAddress = CustomerAddres::create($billing_data);
                }
            } else {

                $customer = Customer::create([
                    'pk_account'       => 2,
                    'customer_name'    => $first_name . ' ' . $last_name,
                    'pk_customer_type' => 1,
                    'email'            => $email,
                    'office_phone'     => $phone,
                    'login_enable'     => 1,
                ]);

                // Get customer id
                $pk_customer_id = $customer->pk_customers;
                $user_data->update([
                    'pk_customers' => $pk_customer_id,
                    'first_name'   => $first_name,
                    'last_name'    => $last_name,
                    'username'     => $username,
                    'email'        => $email,
                    'phone'        => $phone,
                ]);

                // Create customer primary address
                if ($request->primary_address) {
                    $primaryState    = State::where('state_code', $request->primary_state_name)->first();
                    $primary_address = [
                        'pk_customers'    => $pk_customer_id,
                        'pk_address_type' => 1,
                        'address'         => $request->primary_address,
                        'address_1'       => $request->primary_address_1,
                        'city'            => $request->primary_city,
                        'pk_states'       => $primaryState->pk_states ?? 1,
                        'pk_country'      => $primaryState->pk_country ?? 1,
                        'zip'             => $request->primary_zip,
                    ];

                    $primaryAddress = CustomerAddres::create($primary_address);
                }


                if ($request->billing_address) {
                    $billingState   = State::where('state_code', $request->billing_state_name)->first();
                    $billing_data   = [
                        'pk_customers'    => $pk_customer_id,
                        'pk_address_type' => 2,
                        'address'         => $request->billing_address,
                        'address_1'       => $request->billing_address_1,
                        'city'            => $request->billing_city,
                        'pk_states'       => $billingState->pk_states ?? 1,
                        'pk_country'      => $billingState->pk_country ?? 1,
                        'zip'             => $request->billing_zip,
                    ];
                    $billingAddress = CustomerAddres::create($billing_data);
                }

            }

            return [
                $customer_data1,
                $pk_user_id,
                $pk_customer_id,
                $primaryAddress ?? null,
                $billingAddress ?? null,
                $validator
            ];
        } catch (Exception $e) {
            throw $e;
        }
    }

    // Other checkout store for auth
    public function otherCheckoutStore(Request $request, $pk_user_id, $pk_customer_id)
    {
        try {
            $user_data = auth()->user();

            $customer_data = Customer::find($pk_customer_id);

            $cartItems = session('oth_cart');

            // Check customer exists or not
            if (!$customer_data) {
                throw ValidationException::withMessages(['error' => 'Customer not found!']);
            }

            // Payment
            $pk_transactions = null;
            $payment_total   = 0;
            $payem_total_qty = 0;

            if (session('oth_cart')) {
                foreach ((array)session('oth_cart') as $orderitempay) {
                    $quantity_payment = !empty($orderitempay['quantity']) ? $orderitempay['quantity'] : 0;
                    if ($quantity_payment > 0) {
                        $payment_total   += $orderitempay['price'] * $quantity_payment;
                        $payem_total_qty += $quantity_payment;
                    }
                }

                if (!empty($user_data->email)) {
                    $user_email = $user_data->email;

                    $request->request->add(['billing_email' => $user_email]);
                }

                $order_no = 'ORD' . str_pad(Order::max('pk_orders') + 1, 8, "0", STR_PAD_LEFT);
                $res      = $this->handleonlinepay($request, $pk_user_id, $payment_total, $payem_total_qty, $order_no);

                if ($res['msg_type'] == 'error_msg') {
                    // New validation exception for payment error
                    throw ValidationException::withMessages(['error' => $res['message_text'] . ', please correct errors!']);
                }

                $pk_transactions = $res['trans_id'];
            }

            // Delivery option
            $deliveryOption = DeliveryOrPickup::where('pk_delivery_or_pickup', @$request->choise_details)->first();

            // Save order
            $save_order = [
                'pk_users'              => $pk_user_id,
                'pk_transactions'       => $pk_transactions,
                'pk_customers'          => $pk_customer_id,
                'pk_delivery_or_pickup' => $request->choise_details ?? 1,
                'subtotal'              => $payment_total,
                'total'                 => $request->amount,
            ];

            if ($request->pk_locations) {
                $save_order['pk_locations'] = $request->pk_locations;
            }

            if ($request->pk_location_times) {
                $save_order['pk_location_times'] = $request->pk_location_times;
            }

            if ($request->store_id) {
                $save_order['pk_locations'] = $request->store_id;
            }

            if ($request->store_time_id) {
                $save_order['pk_location_times'] = $request->store_time_id;
            }

            if (isset($request->deleveryCast1)) {
                $save_order['delivery_charge'] = $request->deleveryCast1;
            }


            if (isset($request->shippingCharge)) {
                $save_order['tax_charge'] = $request->shippingCharge;
            }

            if (isset($request->discountCharge)) {
                $coupon = explode(" ", $request->discountCharge);

                if ($coupon[1] === '%') {
                    $save_order['discount_charge']      = $coupon[0];
                    $save_order['coupon_discount_type'] = 'percent';
                } elseif ($coupon[0] === '$') {
                    $save_order['discount_charge']      = $coupon[1];
                    $save_order['coupon_discount_type'] = 'fixed';
                } else {
                    $save_order['discount_charge']      = $request->discountCharge[0];
                    $save_order['coupon_discount_type'] = $request->discountCharge[1];
                }
            }

            if (isset($request->estimated_del)) {
                $save_order['estimated_del'] = Carbon::parse($request->estimated_del)->format('Y-m-d');
            }

            $save_order['pk_order_status'] = 1;

            if ($deliveryOption->delivery_or_pickup == 'Store Pickup') {
                $save_order['pickup_date']     = Carbon::parse($request->pickup_date)->format('Y-m-d');
                $save_order['delivery_charge'] = null;
                $save_order['estimated_del']   = null;
                $save_order['delivery_date']   = null;
            }

            if ($cartItems && count($cartItems) > 0 && count($cartItems) == 1) {
                // get first key
                $firstKey                      = array_key_first($cartItems);
                $save_order['delivery_charge'] = $cartItems[$firstKey]['delivery_charge'] ?? null;
                $save_order['estimated_del']   = $cartItems[$firstKey]['estimated_del'] ?? null;
                $save_order['delivery_date']   = isset($cartItems[$firstKey]['delivery_date']) ?
                    Carbon::parse($cartItems[$firstKey]['delivery_date'])->format('Y-m-d') : null;
            }

            // Create order
            $get_order = Order::create($save_order);

            if ($cartItems) {
                $total           = 0;
                $deliveryCharges = 0;
                // $sameAsBilling   = 1;
                $duplicateAddresses = [];

                foreach ($cartItems as $key => $orderitem) {
                    $quantity = $orderitem['quantity'] ?? 0;

                    if ($quantity > 0) {
                        $total += $orderitem['price'] * $quantity;

                        $save_order_item = [
                            'pk_orders'           => $get_order->pk_orders,
                            'pk_shipping_address' => 1,
                            'pk_arrangement_type' => $orderitem['pk_arrangement_type'] ?? '',
                            'name'                => $orderitem['name'] ?? '',
                            'description'         => $orderitem['description'] ?? '',
                            'quantity'            => $quantity,
                            'price'               => $orderitem['price'] ?? 0,
                            'card_message'        => $orderitem['card_message'] ?? '',
                        ];

                        $orderItem = OrderItem::create($save_order_item);

                        // Create item address
                        if ($deliveryOption->delivery_or_pickup == 'Delivery') {
                            $user_name  = $user_data ? $user_data->first_name . ' ' . $user_data->last_name :
                                $request->first_name . ' ' . $request->last_name;
                            $user_email = !$user_data ? $request->email : $user_data->email;
                            $user_phone = !$user_data ? $request->phone : $user_data->phone;

                            if (isset($request->item_address) && count($request->item_address)) {
                                $itemAddress = $request->item_address;
                                $itemAddr    = $itemAddress[$key] ?? null;
                                $address     = $itemAddr['shipping_address'] . ' ' . $itemAddr['shipping_address_1'] . ' ' .
                                    $itemAddr['shipping_city'] . ' ' . $itemAddr['shipping_state_name'] . ' ' .
                                    $itemAddr['shipping_zip'] . ' ' . $itemAddr['delivery_date'];

                                if ($itemAddress[$key]['same_as_billing'] == 0 && !in_array($address, $duplicateAddresses)) {
                                    $deliveryCharges += $itemAddress[$key]['delivery_charge'];
                                } else {
                                    $itemAddress[$key]['same_as_billing'] = 1;
                                }

                                $cusAddr            = @$user_data->customer->address[0];
                                $shipping_address   = $itemAddress[$key]['shipping_address'] ?? $request->billing_address ??
                                    $cusAddr->address ?? '';
                                $shipping_address_1 = $itemAddress[$key]['shipping_address_1'] ?? $request->billing_address_1 ??
                                    $cusAddr->address_1 ?? '';
                                $shipping_city      = $itemAddress[$key]['shipping_city'] ?? $request->billing_city
                                    ?? $cusAddr->city ?? '';
                                $shipping_zip       = $itemAddress[$key]['shipping_zip'] ?? $request->billing_zip
                                    ?? $cusAddr->zip ?? '';
                                $state              = State::where('state_code', $itemAddress[$key]['shipping_state_name']
                                    ?? $request->billing_state_name ?? '')->first();
                                $delivery_date      = $itemAddress[$key]['delivery_date'] ?? $request->delivery_date ?? null;
                                $shipping_data      = [
                                    'pk_customers'       => $pk_customer_id,
                                    'pk_order_items'     => $orderItem->pk_order_items ?? 1,
                                    'shipping_full_name' => $itemAddress[$key]['shipping_full_name'] ?? $user_name,
                                    'shipping_email'     => $itemAddress[$key]['shipping_email'] ?? $user_email,
                                    'shipping_phone'     => $itemAddress[$key]['shipping_phone'] ?? $user_phone,
                                    'shipping_address'   => $shipping_address,
                                    'shipping_address_1' => $shipping_address_1,
                                    'shipping_city'      => $shipping_city,
                                    'pk_states'          => $state->pk_states ?? $cusAddr->pk_states ?? 1,
                                    'pk_country'         => $state->pk_country ?? $cusAddr->pk_country ?? 1,
                                    'shipping_zip'       => $shipping_zip,
                                    'delivery_charge'    => $itemAddress[$key]['delivery_charge'] ?? 0,
                                    'same_as_billing'    => $itemAddress[$key]['same_as_billing'] ?? 1,
                                    'delivery_date'      => Carbon::parse($delivery_date)->format('Y-m-d'),
                                ];

                                // $sameAsBilling = $itemAddress[$key]['same_as_billing'] ?? 1;

                                if ($shipping_address) {
                                    Addres::create($shipping_data);

                                    // Create customer address
                                    CustomerAddres::updateOrCreate([
                                        'pk_customers'    => $pk_customer_id,
                                        'pk_address_type' => 0,
                                        'address'         => $shipping_address,
                                        'city'            => $shipping_city,
                                        'zip'             => $shipping_zip,
                                        'pk_states'       => $state->pk_states ?? $cusAddr->pk_states ?? 1,
                                    ], [
                                        'address_1'  => $shipping_address_1,
                                        'pk_country' => $state->pk_country ?? $cusAddr->pk_country ?? 1,
                                        'lat'        => $itemAddress[$key]['shipping_lat'] ?? null,
                                        'lng'        => $itemAddress[$key]['shipping_lng'] ?? null,
                                    ]);
                                }

                                $duplicateAddresses[$key] = $address;
                            }
                        }
                    }
                }

                // Calculate discount from coupon discount
                $save_order['discountCharge'] = 0;
                if (isset($request->discountCharge)) {
                    if ($save_order['coupon_discount_type'] == 'percent') {
                        $save_order['discountCharge'] = ($total * $save_order['discount_charge']) / 100;
                    } else {
                        $save_order['discountCharge'] = $save_order['discount_charge'];
                    }
                }

                if ($deliveryOption->delivery_or_pickup == 'Delivery') {
                    $deliveryCharges = $deliveryCharges > 0 ? $deliveryCharges : $request->deleveryCast1;
                    $taxTotal        = ($total * $request->shippingCharge) / 100;
                    $total           += ($deliveryCharges + $taxTotal) - $save_order['discountCharge'];
                    $get_order->update([
                        'total'           => $total,
                        'delivery_charge' => $deliveryCharges
                    ]);
                } else {
                    $taxTotal = ($total * $request->shippingCharge) / 100;
                    $total    += $taxTotal - $save_order['discountCharge'];
                    $get_order->update([
                        'total'           => $total,
                        'delivery_charge' => null,
                    ]);
                }
            }

            return [
                $get_order,
                $save_order
            ];
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Handle online payment / Complete transaction for auth
     *
     * @param $request
     * @param $user_id
     * @param $totla_amount
     * @param $qty
     * @param $order_no
     * @return array
     */
    private function handleonlinepay($request, $user_id, $totla_amount, $qty, $order_no)
    {
        $input = $request->input();
        //  echo "<pre>"; print_R($request->all()); die;
        if (auth()->check() && isset($input['address'])) {
            $billing_address    = $request->primary_address;
            $billing_city       = $request->primary_city;
            $billing_state_name = $request->primary_state_name;
            $billing_zip        = $request->primary_zip;
            $billing_email      = auth()->user()->email;
        } else {
            $billing_address    = $request->billing_address ?? '';
            $billing_city       = $request->billing_city ?? '';
            $billing_state_name = $request->billing_state_name ?? '';
            $billing_zip        = $request->billing_zip ?? '';
            $billing_email      = $request->billing_email ?? '';
        }
        /* Create a merchantAuthenticationType object with authentication details
          retrieved from the constants file */
        $merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
        //$merchantAuthentication->setName(env('MERCHANT_LOGIN_ID'));
        $merchantAuthentication->setName('4Y5pCy8Qr');
        //$merchantAuthentication->setTransactionKey(env('MERCHANT_TRANSACTION_KEY'));
        $merchantAuthentication->setTransactionKey('4ke43FW8z3287HV5');

        // Set the transaction's refId
        $refId      = 'ref' . time();
        $cardNumber = preg_replace('/\s+/', '', $input['cc_number']);

        // Create the payment data for a credit card
        $creditCard = new AnetAPI\CreditCardType();
        $creditCard->setCardNumber($cardNumber);
        $creditCard->setExpirationDate($input['expiry_year'] . "-" . $input['expiry_month']);
        $creditCard->setCardCode($input['cvv']);

        // Add the payment data to a paymentType object
        $paymentOne = new AnetAPI\PaymentType();
        $paymentOne->setCreditCard($creditCard);

        // Create order information
        $order = new AnetAPI\OrderType();
        $order->setInvoiceNumber($order_no);
        $order->setDescription('KBT');


        // Set the customer's Bill To address
        $customerAddress = new AnetAPI\CustomerAddressType();
        $customerAddress->setFirstName($input['cc_name']);
        $customerAddress->setLastName($input['cc_name']);

        if (auth()->check() && !isset($input['address'])) {
            $authUser            = auth()->user();
            $cusAddr             = $authUser->customer->address[0] ?? null;
            $input['email']      = $authUser->customer->email;
            $input['address']    = $cusAddr->address ?? $request->primary_address;
            $input['city']       = $cusAddr->city ?? $request->primary_city;
            $input['state_name'] = $cusAddr->state->state_name ?? $request->primary_state_name ?? 'CA';
            $input['zip']        = $cusAddr->zip ?? $request->primary_zip;
        }

        $customerAddress->setAddress($input['address'] ?? $billing_address);
        $customerAddress->setCity($input['city'] ?? $billing_city);
        $customerAddress->setState($input['state_name'] ?? $billing_state_name);
        $customerAddress->setZip($input['zip'] ?? $billing_zip);
        $customerAddress->setCountry('United States');

        // Set the customer's identifying information
        $customerData = new AnetAPI\CustomerDataType();
        $customerData->setEmail($input['email'] ?? $billing_email);

        // Create a TransactionRequestType object and add the previous objects to it
        $transactionRequestType = new AnetAPI\TransactionRequestType();
        $transactionRequestType->setTransactionType("authCaptureTransaction");
        $transactionRequestType->setAmount($totla_amount);
        $transactionRequestType->setOrder($order);
        $transactionRequestType->setPayment($paymentOne);
        $transactionRequestType->setBillTo($customerAddress);

        // Assemble the complete transaction request
        $requests = new AnetAPI\CreateTransactionRequest();
        $requests->setMerchantAuthentication($merchantAuthentication);
        $requests->setRefId($refId);
        $requests->setTransactionRequest($transactionRequestType);

        // Create the controller and get the response
        $controller = new AnetController\CreateTransactionController($requests);
        $response   = $controller->executeWithApiResponse(\net\authorize\api\constants\ANetEnvironment::SANDBOX);
        //$response = $controller->executeWithApiResponse(\net\authorize\api\constants\ANetEnvironment::PRODUCTION);

        $trans_id = 0;
        if ($response != null) {
            // Check to see if the API request was successfully received and acted upon
            if ($response->getMessages()->getResultCode() == "Ok") {
                // Since the API request was successful, look for a transaction response
                // and parse it to display the results of authorizing the card
                $tresponse = $response->getTransactionResponse();

                if ($tresponse != null && $tresponse->getMessages() != null) {
                    //                    echo " Successfully created transaction with Transaction ID: " . $tresponse->getTransId() . "\n";
                    //                    echo " Transaction Response Code: " . $tresponse->getResponseCode() . "\n";
                    //                    echo " Message Code: " . $tresponse->getMessages()[0]->getCode() . "\n";
                    //                    echo " Auth Code: " . $tresponse->getAuthCode() . "\n";
                    //                    echo " Description: " . $tresponse->getMessages()[0]->getDescription() . "\n";
                    $message_text = $tresponse->getMessages()[0]->getDescription() . ", Transaction ID: " . $tresponse->getTransId();
                    $msg_type     = "success_msg";

                    $trans_id = Transaction::create([
                        'amount'         => $totla_amount,
                        'response_code'  => $tresponse->getResponseCode(),
                        'transaction_id' => $tresponse->getTransId(),
                        'auth_id'        => $tresponse->getAuthCode(),
                        'message_code'   => $tresponse->getMessages()[0]->getCode(),
                        'name_on_card'   => trim($input['cc_name']),
                        'account_type'   => $tresponse->getAccountType(),
                        'currency'       => 'USD',
                        'created_by'     => $user_id,
                        'quantity'       => $qty
                    ])->pk_transactions;
                } else {
                    $message_text = 'There were some issue with the payment. Please try again later.';
                    $msg_type     = "error_msg";

                    if ($tresponse->getErrors() != null) {
                        $message_text = $tresponse->getErrors()[0]->getErrorText();
                        $msg_type     = "error_msg";
                    }
                }
                // Or, print errors if the API request wasn't successful
            } else {
                $message_text = 'There were some issue with the payment. Please try again later.';
                $msg_type     = "error_msg";

                $tresponse = $response->getTransactionResponse();

                if ($tresponse != null && $tresponse->getErrors() != null) {
                    $message_text = $tresponse->getErrors()[0]->getErrorText();
                    $msg_type     = "error_msg";
                } else {
                    $message_text = $response->getMessages()->getMessage()[0]->getText();
                    $msg_type     = "error_msg";
                }
            }
        } else {
            $message_text = "No response returned";
            $msg_type     = "error_msg";
        }

        return [
            'msg_type'     => $msg_type,
            'message_text' => $message_text,
            'trans_id'     => $trans_id
        ];
    }

    public function removeOtherCheckoutSession()
    {
        session()->forget('oth_cart');
        session()->forget('oth_checkout_preview');
        session()->forget('oth_total_quantity');
        session()->forget('oth_total_hit');
    }
}
