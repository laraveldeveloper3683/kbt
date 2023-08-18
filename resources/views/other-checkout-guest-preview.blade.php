@extends('layouts.frontend')

@section('title', 'Order Preview')

@section('content')
    <style>
        .modal-backdrop.show {
            display: none !important;
        }
    </style>

    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card mb-2">
                    <div class="card-header">
                        <h4 class="card-title text-center">
                            ORDER PREVIEW
                        </h4>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title text-center">
                                    Customer Information
                                </h4>
                            </div>
                            <div class="card-body">
                                <table class="table table-bordered table-striped">
                                    <tr>
                                        <th>First Name</th>
                                        <td>
                                            {{ @$data['first_name'] }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Last Name</th>
                                        <td>
                                            {{ @$data['last_name'] }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Username</th>
                                        <td>
                                            {{ @$data['username'] }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Phone</th>
                                        <td>
                                            {{ @$data['phone'] }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Email</th>
                                        <td>
                                            {{ @$data['email'] }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Delivery Option</th>
                                        <td>
                                            {{ @$deliveryOption->delivery_or_pickup }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Coupon Code</th>
                                        <td>
                                            {{ @strtoupper(@$data['couponCode']) }}
                                        </td>
                                    </tr>

                                    @if($deliveryOption->delivery_or_pickup == 'Delivery' && $sameAsBilling)
                                        <tr>
                                            <th>Estimated Delivery</th>
                                            <td>
                                                {{ @$data['delivery_date'] }}
                                            </td>
                                        </tr>
                                        {{--<tr>
                                            <th>Selected Delivery Date</th>
                                            <td>
                                                {{ @$data['delivery_date'] }}
                                            </td>
                                        </tr>--}}
                                    @endif

                                    @if($deliveryOption->delivery_or_pickup == 'Store Pickup')
                                        <tr>
                                            <th>Selected Pickup Date</th>
                                            <td>
                                                {{ @$data['pickup_date'] }}
                                            </td>
                                        </tr>
                                    @endif
                                </table>

                                <div class="row">
                                    <div class="col-md-6">
                                        <h5 class="card-title text-center">Primary Address</h5>
                                        <table class="table table-bordered table-striped">
                                            <tr>
                                                <th>Address</th>
                                                <td>{{ @$data['primary_address'] }}</td>
                                            </tr>
                                            <tr>
                                                <th>Address 1</th>
                                                <td>{{ @$data['primary_address_1'] }}</td>
                                            </tr>
                                            <tr>
                                                <th>City</th>
                                                <td>{{ @$data['primary_city'] }}</td>
                                            </tr>
                                            <tr>
                                                <th>State</th>
                                                <td>{{ @$data['primary_state_name'] }}</td>
                                            </tr>
                                            <tr>
                                                <th>Zip</th>
                                                <td>{{ @$data['primary_zip'] }}</td>
                                            </tr>
                                            <tr>
                                                <th>Country</th>
                                                <td>USA</td>
                                            </tr>
                                        </table>
                                    </div>

                                    <div class="col-md-6">
                                        <h5 class="card-title text-center">Billing Address</h5>
                                        <table class="table table-bordered table-striped">
                                            <tr>
                                                <th>Address</th>
                                                <td>{{ @$data['billing_address'] }}</td>
                                            </tr>
                                            <tr>
                                                <th>Address 1</th>
                                                <td>{{ @$data['billing_address_1'] }}</td>
                                            </tr>
                                            <tr>
                                                <th>City</th>
                                                <td>{{ @$data['billing_city'] }}</td>
                                            </tr>
                                            <tr>
                                                <th>State</th>
                                                <td>{{ @$data['billing_state_name'] }}</td>
                                            </tr>
                                            <tr>
                                                <th>Zip</th>
                                                <td>{{ @$data['billing_zip'] }}</td>
                                            </tr>
                                            <tr>
                                                <th>Country</th>
                                                <td>USA</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title text-center">Order Items</h4>
                            </div>
                            <div class="card-body">
                                <table class="table table-bordered table-striped">
                                    <tr>
                                        <th>Item</th>
                                        <th class="text-center">Qty</th>
                                        <th>Price</th>
                                    </tr>
                                    @php
                                        $subtotal = 0;
                                        $total = 0;
                                    @endphp
                                    @foreach(@$cartItems as $item)
                                        @php
                                            $subtotal += @$item['quantity'] * @$item['price'];
                                            $total += @$item['quantity'] * @$item['price'];
                                        @endphp
                                        <tr>
                                            <td>{{ @$item['name'] }}</td>
                                            <td class="text-center">{{ @$item['quantity'] }}</td>
                                            <td>${{ @number_format(@$item['price'], 2) }}</td>
                                        </tr>
                                    @endforeach
                                    <tr>
                                        <th colspan="2" class="text-right">Subtotal</th>
                                        <td>${{ @number_format(@$subtotal, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <th colspan="2" class="text-right">Delivery Charge</th>
                                        <td>${{ @number_format(@$deliveryCharge , 2)}}</td>
                                    </tr>
                                    <tr>
                                        <th colspan="2" class="text-right">Tax</th>
                                        <td>${{ @$data['shippingCharge'] }}</td>
                                    </tr>
                                    @if($discountedAmount > 0)
                                        <tr>
                                            <th colspan="2" class="text-right">Discount (-)</th>
                                            @if(@$data['coupon_discount_type'] == 'percent')
                                                <td>
                                                    {{ @$data['discountCharge'] }} -
                                                    (${{ number_format($discountedAmount, 2) }})
                                                </td>
                                            @else
                                                <td>
                                                    {{ @$data['discountCharge'] }}
                                                </td>
                                            @endif
                                        </tr>
                                    @endif
                                    @php
                                        $total += (@$deliveryCharge + @$data['shippingCharge']) - @$discountedAmount;
                                    @endphp
                                    <tr>
                                        <th colspan="2" class="text-right">Total</th>
                                        <th>${{ @number_format(@$total, 2) }}</th>
                                    </tr>
                                </table>
                            </div>
                            <div class="card-footer">
                                <a href="/other-checkout" class="btn btn-primary">
                                    <i class="fa fa-angle-left"></i>
                                    Back
                                </a>
                                <a href="{{ route('other-checkout-payment') }}" class="btn btn-primary float-right">
                                    Confirm Payment
                                    <i class="fa fa-angle-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 mx-auto">
                    <div class="card mt-4 mb-5">
                        <div class="card-header">
                            <h4 class="card-title text-center">
                                Shipping Address
                            </h4>
                        </div>
                        <div class="card-body">
                            @if(!$sameAsBilling)
                                @forelse($data['item_address'] as $key => $item)
                                    <h4 class="h4 text-center">Address For
                                        - {{ isset($cartItems[$key]['name']) ? $cartItems[$key]['name'] : $loop->index + 1 }}</h4>
                                    <table class="table table-hover table-bordered">

                                        <tr>
                                            <th>Full Name</th>
                                            <td>{{ @$item['shipping_full_name'] }}</td>
                                        </tr>
                                        <tr>
                                            <th>Phone</th>
                                            <td>{{ @$item['shipping_phone'] }}</td>
                                        </tr>
                                        <tr>
                                            <th>Address</th>
                                            <td style="width: 365px;">
                                                {{ @$item['shipping_address'] }}
                                                {{ isset($item['shipping_address_1']) ?
                                                    '#'.$item['shipping_address_1'] : ''}}
                                                </br>
                                                {{ @$item['shipping_city'] }}
                                                {{ @$item['shipping_state_name'] }}
                                                {{ @$item['shipping_zip'] }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Delivery Charge</th>
                                            <td>
                                                ${{ @$item['same_as_billing'] ? '0.00' : @number_format(@$item['delivery_charge'], 2) }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Selected Delivery Date</th>
                                            <td>
                                                {{ @$item['same_as_billing'] ? '' : @$item['delivery_date'] }}
                                            </td>
                                        </tr>
                                    </table>
                                @empty
                                    <table class="table table-hover table-bordered">
                                        <tr>
                                            <td>
                                                No shipping address found!
                                            </td>
                                        </tr>
                                    </table>
                                @endforelse
                            @else
                                <p class="text-center">
                                    Same as billing address!
                                </p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
