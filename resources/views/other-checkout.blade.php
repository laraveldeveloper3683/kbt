@extends('layouts.frontend')

@section('title', 'Cart')

@section('content')
    <style>
        .loader {
            border: 16px solid #f3f3f3;
            border-radius: 50%;
            border-top: 16px solid #3498db;
            width: 60px;
            height: 60px;
            -webkit-animation: spin 2s linear infinite;
            /* Safari */
            animation: spin 2s linear infinite;
        }

        /* Safari */
        @-webkit-keyframes spin {
            0% {
                -webkit-transform: rotate(0deg);
            }

            100% {
                -webkit-transform: rotate(360deg);
            }
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        .loader1 {
            border: 16px solid #f3f3f3;
            border-radius: 50%;
            border-top: 16px solid #3498db;
            width: 20px;
            height: 20px;
            -webkit-animation: spin 2s linear infinite;
            /* Safari */
            animation: spin1 2s linear infinite;
        }

        /* Safari */
        @-webkit-keyframes spin1 {
            0% {
                -webkit-transform: rotate(0deg);
            }

            100% {
                -webkit-transform: rotate(360deg);
            }
        }

        @keyframes spin1 {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }
    </style>

    <div class="container">
        <div class="py-3 text-center">
            <h2>Checkout</h2>
        </div>
        <div class="row">
            {{-- Cart Items --}}
            <div class="col-md-4 order-md-2 mb-4">
                <h4 class="d-flex justify-content-between align-items-center mb-3">
                    <span class="text-muted">Your Cart</span>
                    <span
                        class="badge badge-secondary badge-pill">{{ session('oth_total_quantity') ? session('oth_total_quantity') : 0 }}</span>
                </h4>
                @php
                    $total = 0;
                    $total_qty   = 0;
                @endphp
                <ul class="list-group mb-3 sticky-top">
                    @if (session('oth_cart'))
                        @foreach ((array) session('oth_cart') as $id => $details)
                            @php
                                $total     += $details['price'] * $details['quantity'];
                                $total_qty += $details['quantity'];
                            @endphp

                            <li class="list-group-item d-flex justify-content-between lh-condensed">
                                <div>
                                    <h6 class="my-0" id="cart-item-name{{ $id }}">{{ $details['name'] }}</h6>
                                    <small class="text-muted">{{ $details['description'] }}</small>
                                </div>
                                <span class="text-muted">${{ $details['price'] * $details['quantity'] }}</span>
                            </li>
                        @endforeach
                    @endif
                    @php
                        $itemAddresses = @$oldData['item_address'] ?? [];
                        $allChecked = 1;
                        $deliveryCharge = 0;
                        foreach ($itemAddresses as $itemAddress) {
                            $allChecked = $itemAddress['same_as_billing'] ?? 1;
                            $deliveryCharge += $itemAddress['delivery_charge'];
                        }
                        if ($deliveryCharge <= 0) {
                            $deliveryCharge = old('deleveryCast1', @$oldData['deleveryCast1']);
                        }
                    @endphp
                    <li class="list-group-item justify-content-between lh-condensed dlCast {{ $allChecked ? 'd-flex' : 'd-none' }}"
                        id="g-delivery-charge">
                        <div class="DeliveryChargeDiv" @if(!$allChecked) style="display: none;" @endif>
                            <h6 class="my-0">Delivery Charge
                                <br><small class="stncity"></small>
                            </h6>
                            <small class="estimate_del"></small>
                        </div>
                        <!-- <span class=""></span> -->
                        <span class="text-muted deleveryCast loade DeliveryChargeDiv"
                              @if(!$allChecked) style="display: none;" @endif>
                            ${{ old('deleveryCast1', @$oldData['deleveryCast1']) ?? 0 }}
                        </span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between lh-condensed dlCast">
                        @php
                            $taxRate = old('shippingCharge', @$oldData['shippingCharge']);
                        @endphp
                        <div class="taxR">
                            @if($taxRate)
                                <h6 class="my-0">
                                    Tax
                                </h6>
                            @endif
                        </div>
                        <span class="text-muted taxRa loade">
                            @if($taxRate)
                                ${{ $taxRate }}
                            @endif
                        </span>

                    </li>
                    <li class="list-group-item d-flex justify-content-between lh-condensed dlCast">
                        @php
                            $discount = old('discountCharge', @$oldData['discountCharge']);
                        @endphp
                        <div class="disc1">
                            @if($discount)
                                <h6 class="my-0">
                                    Discount (-)
                                </h6>
                            @endif
                        </div>
                        <span class="text-muted disc loadeds">
                            @if($discount)
                                {{ $discount }}
                            @endif
                        </span>

                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        @php
                            $discountedAmount = 0;

                            $grandTotal = $total + $deliveryCharge + $taxRate;

                            if (isset($oldData['discountCharge'])) {
                                $couponCharge = explode(" ", @$oldData['discountCharge']);

                                if ($couponCharge[1] === '%') {
                                    $discountedAmount             = $grandTotal * @$couponCharge[0] / 100;
                                } elseif ($couponCharge[0] === '$') {
                                    $discountedAmount             = $couponCharge[1];
                                } else {
                                    $discountCharge               = $data['discountCharge'][0];
                                    $discountedAmount             = $grandTotal - $discountCharge;
                                }
                            }

                            $grandTotal = $grandTotal - $discountedAmount;
                        @endphp

                        <span>Total (USD)</span>
                        <strong
                            class="totalCast1 loade">${{ $grandTotal > 0 ? number_format($grandTotal, 2) : number_format($total, 2) }}</strong>
                        <input type="hidden" value="{{ $total }}" class="totalCast">
                    </li>
                </ul>

                @if($allChecked == 1)
                    <ul class="list-group mb-3 sticky-top" id="cart-item-delivery-charges"
                        style="display: none;">
                    </ul>
                @else
                    <ul class="list-group mb-3 sticky-top" id="cart-item-delivery-charges">
                        @if(@count(@$itemAddresses))
                            @php
                                $cartItems = session('oth_cart') ?? [];
                            @endphp
                            @foreach(@$itemAddresses as $ik => $itemAddress)
                                <li class="list-group-item d-flex justify-content-between lh-condensed"
                                    id="delivery-charge-item{{ $ik }}">
                                    <h6 class="my-0">
                                        Delivery Charge For <strong>{{ $cartItems[$ik]['name'] }}</strong>
                                        <br>
                                        <small>
                                            delivering from
                                            {{ @$itemAddress['store_city'] }}, {{ @$itemAddress['store_name'] }}
                                        </small>
                                        <br>
                                        <small>Estimdated Delivery, {{ @$oldData['estimated_del'] }}</small>
                                    </h6>

                                    <span class="text-muted">${{ @$itemAddress['delivery_charge'] }}</span>
                                </li>
                            @endforeach
                        @endif
                    </ul>
                @endif

                <div class="form-group">
                    <label>Apply Coupon (If you have)</label>
                    <input type="text" name="coupon" class="form-control couponApply" onKeyup="couponApply(this.value)"
                           value="{{ old('couponCode', @$oldData['couponCode']) }}">
                </div>

            </div>

            <div class="col-md-8 order-md-1">
                <form action="{{ route('other-checkout-preview-post') }}" method="POST">
                    @csrf

                    <h4 class="mb-3">User Details</h4>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label
                                for="first_name">
                                {{ $user_data->first_name . ' ' . $user_data->last_name }}
                                {{ $user_data->email ? '(' . $user_data->email . ')' : '' }}
                            </label>
                        </div>
                    </div>

                    <div class="row">
                        @if(!$user_data->first_name)
                            <div class="col-md-6 mb-3">
                                <label for="first_name">First name</label>
                                <input type="text" class="form-control" id="first_name" name="first_name"
                                       value="{{ old('first_name', @$oldData['first_name']) }}">
                                @error('first_name')
                                <span class="invalid-feedback d-block" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        @else
                            <input type="hidden" name="first_name"
                                   value="{{ old('first_name', @$oldData['first_name'] ?? @$user_data->first_name) }}">
                        @endif

                        @if(!$user_data->last_name)
                            <div class="col-md-6 mb-3">
                                <label for="lastName">Last name</label>
                                <input type="text" class="form-control" id="last_name" name="last_name"
                                       value="{{ old('last_name', @$oldData['last_name']) }}">
                                @error('last_name')
                                <span class="invalid-feedback d-block" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        @else
                            <input type="hidden" name="last_name"
                                   value="{{ old('last_name', @$oldData['last_name'] ?? @$user_data->last_name) }}">
                        @endif
                    </div>

                    <div class="row">
                        @if(!$user_data->username)
                            <div
                                class="mb-3 {{ !$user_data->phone && !$user_data->username ? 'col-md-6' : 'col-md-12' }}">
                                <label for="username">Username</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="username" name="username"
                                           value="{{ old('username', @$oldData['username']) }}">
                                    @error('username')
                                    <span class="invalid-feedback d-block" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        @else
                            <input type="hidden" name="username"
                                   value="{{ old('username', @$oldData['username'] ?? @$user_data->username) }}">
                        @endif

                        {{--@if(!$user_data->phone || $user_data->phone == '1')
                            <div
                                class="mb-3 {{ !$user_data->phone && !$user_data->username ? 'col-md-6' : 'col-md-12' }}">
                                <label for="phone">Mobile No. <span class="text-muted"></span></label>
                                <input type="text" class="form-control" id="phone" name="phone"
                                       value="{{ old('phone', @$oldData['phone']) }}">
                                @error('phone')
                                <span class="invalid-feedback d-block" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        @else
                            <input type="hidden" name="phone"
                                   value="{{ old('phone', @$oldData['phone'] ?? @$user_data->phone) }}">
                        @endif--}}

                        @if(!$user_data->email)
                            <div class="col-md-12 mb-3">
                                <label for="email">Email <span class="text-muted"></span></label>
                                <input type="email" class="form-control" id="email" name="email"
                                       value="{{ old('email', @$oldData['email']) }}">
                                @error('email')
                                <span class="invalid-feedback d-block" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        @else
                            <input type="hidden" name="email"
                                   value="{{ old('email', @$oldData['email'] ?? @$user_data->email) }}">
                        @endif

                    </div>

                    @if(!count($kbt_address) || !$primaryAddress || !$billingAddress)
                        <strong>
                            Enter location details and choose Store Pickup to see store list
                        </strong>
                    @endif

                    @if (!count($kbt_address) || !$primaryAddress)
                        <div class="mb-3 mt-4">
                            <label for="address">Address</label>
                            <input type="text" class="form-control" id="primary_address" name="primary_address"
                                   value="{{ old('primary_address', @$oldData['primary_address']) }}">
                            @error('primary_address')
                            <span class="invalid-feedback d-block" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="address_1">Address 2 <span class="text-muted">(Optional)</span></label>
                            <input type="text" class="form-control" id="primary_address_1" name="primary_address_1"
                                   value="{{ old('primary_address_1', @$oldData['primary_address_1']) }}">
                            @error('primary_address_1')
                            <span class="invalid-feedback d-block" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">City</label>
                                    <input type="text" name="primary_city" id="primary_city" class="form-control"
                                           value="{{ old('primary_city', @$oldData['primary_city']) }}"
                                           onkeypress="return RestrictCommaSemicolon(event);" ondrop="return false;"
                                           onpaste="return false;">
                                    @error('primary_city')
                                    <span class="invalid-feedback d-block" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">State</label>
                                    <input id="primary_state_name" type="text"
                                           name="primary_state_name"
                                           class="form-control"
                                           value="{{ old('primary_state_name', @$oldData['primary_state_name']) }}">
                                    @error('primary_state_name')
                                    <span class="invalid-feedback d-block" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Zip</label>
                                    <input type="text" id="primary_postal_code" name="primary_zip"
                                           class="form-control"
                                           value="{{ old('primary_zip', @$oldData['primary_zip']) }}">
                                    @error('primary_zip')
                                    <span class="invalid-feedback d-block" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Country</label>
                                    <input id="primary_country" type="text" name="primary_country_name"
                                           class="form-control" readonly
                                           value="{{ old('primary_country_name', @$oldData['primary_country_name'] ?? 'USA') }}">
                                    @error('primary_country_name')
                                    <span class="invalid-feedback d-block" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    @else
                        <input type="hidden" name="primary_address"
                               value="{{ old('primary_address', @$oldData['primary_address'] ?? @$primaryAddress->address) }}">
                        <input type="hidden" name="primary_address_1"
                               value="{{ old('primary_address_1', @$oldData['primary_address_1'] ?? @$primaryAddress->address_1) }}">
                        <input type="hidden" name="primary_city"
                               value="{{ old('primary_city', @$oldData['primary_city'] ?? @$primaryAddress->city) }}">
                        <input type="hidden" name="primary_state_name"
                               value="{{ old('primary_state_name', @$oldData['primary_state_name'] ?? @$primaryState->state_code) }}">
                        <input type="hidden" name="primary_zip"
                               value="{{ old('primary_zip', @$oldData['primary_zip'] ?? @$primaryAddress->zip) }}">
                        <input type="hidden" name="primary_country_name"
                               value="{{ old('primary_country_name', @$oldData['primary_country_name'] ?? 'USA') }}">
                    @endif

                    @if(!count($kbt_address) || !$primaryAddress || !$billingAddress)
                        <hr class="mb-4">
                    @endif

                    @if(!count($kbt_address) || !$billingAddress)
                        <div id="billing-address-section">
                            <h4 class="mb-3">Billing Address</h4>

                            <div class="form-group">
                                <label for="billing_address">Address</label>
                                <input type="text" class="form-control" id="billing_address"
                                       name="billing_address"
                                       value="{{ old('billing_address', @$oldData['billing_address']) }}">
                                @error('billing_address')
                                <span class="invalid-feedback d-block" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="billing_address_1">Address 2 <span
                                        class="text-muted">(Optional)</span></label>
                                <input type="text" class="form-control" id="billing_address_1"
                                       name="billing_address_1"
                                       value="{{ old('billing_address_1', @$oldData['billing_address_1']) }}">
                                @error('billing_address_1')
                                <span class="invalid-feedback d-block" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">City</label>
                                        <input type="text" id="billing_city" name="billing_city"
                                               class="form-control billingCity"
                                               value="{{ old('billing_city', @$oldData['billing_city']) }}">
                                        @error('billing_city')
                                        <span class="invalid-feedback d-block" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">State</label>
                                        <input type="text" id="billing_state_name" name="billing_state_name"
                                               class="form-control"
                                               value="{{ old('billing_state_name', @$oldData['billing_state_name']) }}">
                                        @error('billing_state_name')
                                        <span class="invalid-feedback d-block" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                </div>

                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Zip</label>
                                        <input type="text" id="billing_zip" name="billing_zip" class="form-control"
                                               value="{{ old('billing_zip', @$oldData['billing_zip']) }}">
                                        @error('billing_zip')
                                        <span class="invalid-feedback d-block" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Country</label>
                                        <input type="text" id="billing_country_name" name="billing_country_name"
                                               class="form-control" readonly
                                               value="{{ old('billing_country_name', @$oldData['billing_country_name'] ?? 'USA') }}">
                                        @error('billing_country_name')
                                        <span class="invalid-feedback d-block" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                </div>

                            </div>
                        </div>
                    @else
                        <input type="hidden" name="billing_address"
                               value="{{ old('billing_address', @$oldData['billing_address'] ?? @$billingAddress->address) }}">
                        <input type="hidden" name="billing_address_1"
                               value="{{ old('billing_address_1', @$oldData['billing_address_1'] ?? @$billingAddress->address_1) }}">
                        <input type="hidden" name="billing_city"
                               value="{{ old('billing_city', @$oldData['billing_city'] ?? @$billingAddress->city) }}">
                        <input type="hidden" name="billing_state_name"
                               value="{{ old('billing_state_name', @$oldData['billing_state_name'] ?? @$billingState->state_code) }}">
                        <input type="hidden" name="billing_zip"
                               value="{{ old('billing_zip', @$oldData['billing_zip'] ?? @$billingAddress->zip) }}">
                        <input type="hidden" name="billing_country_name"
                               value="{{ old('billing_country_name', @$oldData['billing_country_name'] ?? 'USA') }}">
                    @endif

                    <hr class="mb-4">

                    @php
                        $is_existing_address = '';
                    @endphp

                    @if($deliveryOptions->count())
                        <div class="">
                            @foreach($deliveryOptions as $deliveryOption)
                                <input type="radio" name="choise_details" onClick="myFun(this);"
                                       value="{{ $deliveryOption->pk_delivery_or_pickup }}"
                                       data-text="{{ $deliveryOption->delivery_or_pickup }}"
                                    {{ $loop->first ? 'checked' : '' }}> {{ Str::title($deliveryOption->delivery_or_pickup) }}
                            @endforeach
                        </div>

                        <div class="form-group mt-4" id="pickup-date-div">
                            <label for="pickup-date" class="form-label">
                                Select Pickup Date
                            </label>
                            <input type="text" name="pickup_date" id="pickup-date"
                                   class="form-control pickup-date @error('pickup_date') is-invalid @enderror"
                                   placeholder="Enter pickup date" required
                                   value="{{ old('pickup_date', @$oldData['pickup_date']) }}">
                            @error('pickup_date')
                            <span class="invalid-feedback d-block" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    @endif

                    <br>

                    @if($kbt_address->count())
                        @php
                            $is_existing_address = 1;
                        @endphp
                    @endif

                    <div class="radio">
                        <label>
                            <input type="radio" checked name="address_type"
                                   onclick="setExisting(this.checked);"
                                   value="existing" id="existing_address">
                            &nbsp;I want to use an existing address
                        </label>

                        <div class="col-md-12 mb-3 pl-0">
                            <select class="custom-select d-block w-100" id="existing_address_id"
                                    name="existing_address_id">
                                @if($kbt_address->count())
                                    @foreach ($kbt_address as $value)
                                        @php
                                            $full_name = $user_data->first_name . ' ' . $user_data->last_name;
                                            $address          = $value->address ? $value->address . ', ' : '';
                                            $address_1        = $value->address_1 ? $value->address_1 . ', ' : '';
                                            $city             = $value->city ? $value->city . ', ' : '';
                                            $state_name       = @$value->state->state_code ? @$value->state->state_code . ', ' : '';
                                            $country_name     = 'USA';
                                            $zip              = $value->zip ? $value->zip : '';
                                            $get_full_address = $full_name . $address . $address_1 . $city . $state_name . $country_name . $zip;
                                        @endphp

                                        <option value="{{ $value->pk_customer_address }}"
                                                data-city="{{ $value->city ?? '' }}"
                                                data-address="{{ $value->address ?? '' }}"
                                                data-address-1="{{ $value->address_1 ?? '' }}"
                                                data-zip="{{ $value->zip ?? '' }}"
                                                class="abcde"
                                            {{ old('existing_address_id', @$oldData['existing_address_id']) == $value->pk_customer_address ? 'selected' : '' }}>
                                            {{ $get_full_address }}
                                        </option>
                                    @endforeach
                                @else
                                    <option value="">No data found!</option>
                                @endif
                            </select>
                            @error('address_type')
                            <span class="invalid-feedback d-block" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror
                        </div>
                    </div>

                    <div class="radio">
                        <label>
                            <input
                                {{ !$is_existing_address && old('address_type', @$oldData['address_type']) == 'new_address' ? 'checked' : '' }} type="radio"
                                onclick="setNewAddress(this.checked);" name="address_type" value="new_address">
                            &nbsp;I want to use a new address
                        </label>
                    </div>

                    <hr>


                    <div class="billing full-address-div"
                         style="{{ old('address_type', @$oldData['address_type']) == 'new_address' ? '' : 'display:none;' }}">

                        <h3 class="mb-3">
                            <strong>
                                Manage Shipping Address for Items&nbsp;
                            </strong>
                        </h3>

                        @php
                            $total = 0;
                            $total_qty   = 0;
                        @endphp

                        @if (session('oth_cart'))
                            @php
                                $addressItems = @$oldData['item_address'] ?? [];
                            @endphp
                            @foreach ((array) session('oth_cart') as $id => $details)
                                @php
                                    $total     += $details['price'] * $details['quantity'];
                                    $total_qty += $details['quantity'];
                                @endphp

                                <div>
                                    <h6 class="my-0" data-name="{{ $id }}">
                                        <strong>{{ $details['name'] }}</strong>
                                    </h6>
                                    <h5>{{ $details['description'] }}</h5>
                                </div>

                                <label for="checkbox{{ $id }}">
                                    <input type="checkbox" id="checkbox{{ $id }}" class="item-address-checkbox"
                                           data-id="{{ $id }}"
                                        {{ old('item_address.'.$id.'.same_as_billing', @$addressItems[$id]['same_as_billing'] ?? 1) ? 'checked' : '' }}>
                                    Use same as Billing Address for this item
                                </label>

                                <div id="div{{ $id }}"
                                     style="{{ old('item_address.'.$id.'.same_as_billing', @$addressItems[$id]['same_as_billing'] ?? 1) ? 'display:none;' : '' }}">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="manage_shipping_full_name{{ $id }}">Name Hello</label>
                                            <input type="text" class="form-control" id="shipping_full_name{{ $id }}"
                                                   name="item_address[{{ $id }}][shipping_full_name]"
                                                   value="{{ old('item_address') &&
                                                        !empty(old('item_address.'.$id.'.shipping_full_name')) ?
                                                        old('item_address.'.$id.'.shipping_full_name') : @$addressItems[$id]['shipping_full_name'] }}">
                                            @error('item_address.'.$id.'.shipping_full_name')
                                            <span class="invalid-feedback d-block" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                            @enderror
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label for="shipping_phone{{ $id }}">Phone</label>
                                            <input type="text" class="form-control" id="shipping_phone{{ $id }}"
                                                   name="item_address[{{ $id }}][shipping_phone]" value="{{ old('item_address') &&
                                                        !empty(old('item_address.'.$id.'.shipping_phone')) ?
                                                        old('item_address.'.$id.'.shipping_phone') : @$addressItems[$id]['shipping_phone'] }}">
                                            @error('item_address.'.$id.'.shipping_phone')
                                            <span class="invalid-feedback d-block" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                            @enderror
                                        </div>

                                    </div>

                                    <div class="row">
                                        <div class="col-md-4 mb-3">
                                            <label for="billing_address{{ $id }}">Address test</label>
                                            <input type="text" class="form-control"
                                                   id="billing_address{{ $id }}"
                                                   name="item_address[{{ $id }}][shipping_address]"
                                                   value="{{ old('item_address') &&
                                                        !empty(old('item_address.'.$id.'.shipping_address')) ?
                                                        old('item_address.'.$id.'.shipping_address') : @$addressItems[$id]['shipping_address'] }}">
                                            @error('item_address.'.$id.'.shipping_address')
                                            <span class="invalid-feedback d-block" role="alert">
                                                  <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>

                                        <div class="col-md-4 mb-3">
                                            <label for="billing_address_1{{ $id }}">Address 2 <span
                                                    class="text-muted">(Optional)</span></label>
                                            <input type="text" class="form-control"
                                                   id="billing_address_1{{ $id }}"
                                                   name="item_address[{{ $id }}][shipping_address_1]"
                                                   value="{{ old('item_address') &&
                                                        !empty(old('item_address.'.$id.'.shipping_address_1')) ?
                                                        old('item_address.'.$id.'.shipping_address_1') : @$addressItems[$id]['shipping_address_1'] }}">
                                            @error('item_address.'.$id.'.shipping_address_1')
                                            <span class="invalid-feedback d-block" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>

                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="form-label">City</label>
                                                <input type="text"
                                                       onkeypress="return RestrictCommaSemicolon(event);"
                                                       ondrop="return false;" onpaste="return false;"
                                                       id="billing_city{{ $id }}"
                                                       name="item_address[{{ $id }}][shipping_city]"
                                                       class="form-control shipping_city"
                                                       value="{{ old('item_address') &&
                                                        !empty(old('item_address.'.$id.'.shipping_city')) ?
                                                        old('item_address.'.$id.'.shipping_city') : @$addressItems[$id]['shipping_city'] }}">

                                                @error('item_address.'.$id.'.shipping_city')
                                                <span class="invalid-feedback d-block" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>

                                    </div>

                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="form-label">State</label>
                                                <input type="text"
                                                       id="billing_state_name{{ $id }}"
                                                       name="item_address[{{ $id }}][shipping_state_name]"
                                                       class="form-control" value="{{ old('item_address') &&
                                                        !empty(old('item_address.'.$id.'.shipping_state_name')) ?
                                                        old('item_address.'.$id.'.shipping_state_name') : @$addressItems[$id]['shipping_state_name'] }}">
                                                @error('item_address.'.$id.'.shipping_state_name')
                                                <span class="invalid-feedback d-block" role="alert">
                                                      <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="form-label">Zip</label>
                                                <input type="text"
                                                       id="shipping_zip{{ $id }}"
                                                       name="item_address[{{ $id }}][shipping_zip]"
                                                       class="form-control"
                                                       value="{{ old('item_address') &&
                                                        !empty(old('item_address.'.$id.'.shipping_zip')) ?
                                                        old('item_address.'.$id.'.shipping_zip') : @$addressItems[$id]['shipping_zip'] }}">
                                                @error('item_address.'.$id.'.shipping_zip')
                                                <span class="invalid-feedback d-block" role="alert">
                                                              <strong>{{ $message }}</strong>
                                                          </span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="form-label">Country</label>
                                                <input type="text"
                                                       id="shipping_country_name{{ $id }}"
                                                       name="item_address[{{ $id }}][shipping_country_name]"
                                                       class="form-control" readonly
                                                       value="{{ old('item_address') &&
                                                        !empty(old('item_address.'.$id.'.shipping_country_name')) ?
                                                        old('item_address.'.$id.'.shipping_country_name', 'USA') : 'USA' }}">
                                                @error('item_address.'.$id.'.shipping_country_name')
                                                <span class="invalid-feedback d-block" role="alert">
                                                              <strong>{{ $message }}</strong>
                                                          </span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-12">
                                            <div class="form-group mt-4">
                                                <label for="pickup-date{{ $id }}" class="form-label">
                                                    Select Pickup Date
                                                </label>
                                                <input type="text" name="item_address[{{ $id }}][pickup_date]"
                                                       id="pickup-date{{ $id }}"
                                                       class="form-control pickup-date"
                                                       placeholder="Enter pickup date"
                                                       value="{{ old('item_address') &&
                                                        !empty(old('item_address.'.$id.'.pickup_date')) ?
                                                        old('item_address.'.$id.'.pickup_date') : @$addressItems[$id]['pickup_date'] }}">
                                                @error('item_address.'.$id.'.pickup_date')
                                                <span class="invalid-feedback d-block" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <input type="hidden" id="is_same_as_billing{{ $id }}"
                                           name="item_address[{{ $id }}][same_as_billing]" value="{{ old('item_address') &&
                                                        !empty(old('item_address.'.$id.'.same_as_billing')) ?
                                                        old('item_address.'.$id.'.same_as_billing') : @$addressItems[$id]['same_as_billing'] }}">

                                    <input type="hidden" id="delivery_charge{{ $id }}"
                                           name="item_address[{{ $id }}][delivery_charge]"
                                           value="{{ old('item_address') &&
                                                        !empty(old('item_address.'.$id.'.delivery_charge')) ?
                                                        old('item_address.'.$id.'.delivery_charge') : @$addressItems[$id]['delivery_charge'] }}">

                                    <input type="hidden" id="estimated_del{{ $id }}"
                                           name="item_address[{{ $id }}][estimated_del]"
                                           value="{{ old('item_address') &&
                                                        !empty(old('item_address.'.$id.'.estimated_del')) ?
                                                        old('item_address.'.$id.'.estimated_del') : @$addressItems[$id]['estimated_del'] }}">

                                    <input type="hidden" id="store_city{{ $id }}"
                                           name="item_address[{{ $id }}][store_city]"
                                           value="{{ old('item_address') &&
                                                        !empty(old('item_address.'.$id.'.store_city')) ?
                                                        old('item_address.'.$id.'.store_city') : @$addressItems[$id]['store_city'] }}">

                                    <input type="hidden" id="store_name{{ $id }}"
                                           name="item_address[{{ $id }}][store_name]"
                                           value="{{ old('item_address') &&
                                                        !empty(old('item_address.'.$id.'.store_name')) ?
                                                        old('item_address.'.$id.'.store_name') : @$addressItems[$id]['store_name'] }}">
                                </div>
                            @endforeach
                        @endif

                        @php
                            $get_address_type = old('address_type', @$oldData['address_type']);
                        @endphp
                    </div>

                    <div class="store full-address-div"
                         style="{{ old('address_type', @$oldData['address_type']) == 'new_address' ? '' : 'display:none;' }}">

                        <div class="loder"></div>
                        <div class="row mt-3 abcd">
                        </div>
                        <div
                            style="{{ (!empty($get_address_type) and !empty(old('is_shipping', @$oldData['is_shipping']))) ? '' : 'display:none;' }}"
                            class="shipping-address-div">
                        </div>
                    </div>


                    <hr class="mb-4">

                    <input type="hidden" class="form-control amountTotal" id="amount" name="amount"
                           value="{{ old('amount', @$oldData['amount'] ?? $total) }}">

                    <input type="hidden" class="form-control deleveryCast1" name="deleveryCast1"
                           value="{{ old('deleveryCast1', @$oldData['deleveryCast1']) }}">

                    <input type="hidden" class="form-control shippingCharge" id="tax_rate" name="shippingCharge"
                           value="{{ old('shippingCharge', @$oldData['shippingCharge']) }}">

                    <input type="hidden" class="form-control discountCharge" name="discountCharge"
                           value="{{ old('discountCharge', @$oldData['discountCharge']) }}">

                    <input type="hidden" id="couponCode" name="couponCode"
                           value="{{ old('couponCode', @$oldData['couponCode']) }}">

                    <input type="hidden" class="form-control pk_locations" name="pk_locations"
                           value="{{ old('pk_locations', @$oldData['pk_locations']) }}">

                    <input type="hidden" class="form-control estimated_del" name="estimated_del"
                           value="{{ old('estimated_del', @$oldData['estimated_del']) }}">

                    <button class="btn btn-primary btn-lg btn-block mb-5" type="submit">Continue to Review</button>
                </form>

            </div>
        </div>
    </div>

    <script
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAB80hPTftX9xYXqy6_NcooDtW53kiIH3A&libraries=places&callback=initAutocomplete"
        async defer></script>

    <script type="text/javascript">
        $(document).ready(function () {
            // Get references to the checkboxes and the target divs
            const checkboxes = $('input[type="checkbox"]');
            const divs = $('[id^="div"]');

            // Handle the change event of all checkboxes
            checkboxes.on("change", function () {
                // Loop through each checkbox
                checkboxes.each(function (index) {
                    // Get the corresponding div based on the index
                    const div = divs.eq(index);

                    // Check the state of the checkbox
                    if ($(this).is(":checked")) {
                        div.hide(); // Show the div when the checkbox is checked
                    } else {
                        div.show(); // Hide the div when the checkbox is unchecked
                    }
                });
            });
        });

        var ifLogin = '{{ auth()->id() }}';
        if (ifLogin) {
            var aadd = $('#existing_address_id').val();
            getAddreessById(aadd);

            $('#existing_address_id').on('change', function () {
                getAddreessById($(this).val());
            });
        }

        function getAddreessById(id) {
            $.ajax({
                url     : "{{ url('getAddressId') }}",
                type    : 'post',
                dataType: 'json',
                data    : {
                    '_token': '{{ csrf_token() }}',
                    id      : id,
                },
                success : function (data) {
                    $('.loder').text("");
                    if (data) {
                        var city = data.city;
                        var address = data.address;
                        shippingCity(city, address);
                    }

                },
                complete: function () {
                    $('.loder').text("");
                },
            })
        }

        $('.dlCast').hide();

        function couponApply(value) {
            //alert(value);
            if (value.length === 0) {
                alert('Please enter coupon code!');
                return false;
            }

            $('#couponCode').val(value);

            // this code
            $.ajax({
                url       : "{{ url('apply-coupon') }}",
                type      : 'post',
                dataType  : 'json',
                data      : {
                    '_token'  : '{{ csrf_token() }}',
                    couponName: value,
                },
                beforeSend: function () {
                    $('.loadeds').html(`<div class="loader1"></div>
                    `);

                },
                success   : function (data) {
                    var totalcast = parseFloat($('.amountTotal').val());
                    if (data[1] == 'fixed') {
                        //$('.amountTotal').val(totalcast-data[0]);
                        var to = totalcast - data[0].toFixed(2);
                        $('.totalCast1').html('$' + to);
                        $('.disc1').html(`<h6 class="my-0">Discount (-)
                                     </h6>`);

                        $('.disc').html('$' + data[0]);
                        $('.discountCharge').val('$' + ' ' + data[0]);
                    }
                    if (data[1] == 'percent') {
                        var to = totalcast - (totalcast * data[0] / 100).toFixed(2);
                        $('.totalCast1').html('$' + to);
                        $('.disc1').html(`<h6 class="my-0">Discount (-)
                                      </h6>`);

                        $('.disc').html(data[0] + '%');
                        $('.discountCharge').val(data[0] + ' ' + '%');
                    }
                },
                complete  : function () {
                    $('.loder').text("");
                },
            })
            //end code
        }

        function addressUpdate(value, fname, city) {
            $("#" + fname).val(value);
            $('.couponApply').val('');
            $('.disc1').html('');
            $('.disc').html('');
            var totalcast = parseFloat($('.totalCast').val());
            $('.amountTotal').val(totalcast);
            var to = totalcast;
            $('.totalCast1').html('$' + to);
            $('.discountCharge').val('');


            if (fname == 'billing_city') {
                var address = $('#billing_address').val();
                var Shipcity = city;
            }

            if (fname == 'primary_address') {
                var address = value;
                var Shipcity = $('#primary_city').val();
            }


            $.ajax({
                url       : "{{ url('other-checkoutss') }}",
                type      : 'post',
                dataType  : 'json',
                data      : {
                    '_token': '{{ csrf_token() }}',
                    city    : Shipcity,
                    address : address
                },
                beforeSend: function () {
                    $('.loade').html(`<div class="loader1"></div>
                            `);

                },
                success   : function (data) {
                    var totalcast = parseFloat($('.totalCast').val());
                    var de = data.cost;
                    $('.deleveryCast').text('$' + de);

                    if ($('input[name="choise_details"]:checked').val() == 'store') {
                        var to = totalcast + parseFloat(data.taxRate);
                    } else {
                        var to = totalcast + parseFloat(data.cost) + parseFloat(data.taxRate);
                    }
                    $('.totalCast1').text('$' + to);
                    $('.stncity').html('<small> delivering from ' + data.storeCity + ',' + data.storeName + '</small>')
                    $('.estimate_del').html('<small> Estimated Delivery ,' + data.Estimated_Delivery_Time + '</small>')
                    $('.estimated_del').val(data.Estimated_Delivery_Time);

                    $('.dlCast').css("display", "block!important");
                    $('.taxR').html(`<h6 class="my-0">Tax
                                    </h6>`);
                    $('.taxRa').html('$' + data.taxRate);
                    $('.amountTotal').val(to);
                    $('.deleveryCast1').val(de);
                    $('.shippingCharge').val(data.taxRate);
                    $('.pk_locations').val(data.pk_location)

                    if ($('input[name="choise_details"]:checked').val() == 'store') {
                        console.log(6);
                        $('.DeliveryChargeDiv').hide();
                    }

                },
                complete  : function () {
                    // $('.loade').text("");
                },
            })

        }

        function shippingCity(city, address) {
            console.log(city, address)
            var billCity = $('.billingCity').val();
            $('.couponApply').val('');
            $('.disc1').html('');
            $('.disc').html('');

            var totalcast = parseFloat($('.totalCast').val());
            $('.amountTotal').val(totalcast);
            var to = totalcast;
            $('.totalCast1').html('$' + to);
            $('.discountCharge').val('');

            if (city == billCity) {
                // this code
                $.ajax({
                    url       : "{{ url('other-checkoutss') }}",
                    type      : 'post',
                    dataType  : 'json',
                    data      : {
                        '_token': '{{ csrf_token() }}',
                        city    : city,
                        address : address
                    },
                    beforeSend: function () {
                        $('.loade').html(`<div class="loader1"></div>
                            `);

                    },
                    success   : function (data) {
                        var totalcast = parseFloat($('.totalCast').val());
                        var de = data.cost;
                        $('.deleveryCast').text('$' + de);
                        if ($('input[name="choise_details"]:checked').val() == 'store') {
                            de = 0;
                            var to = totalcast + parseFloat(data.taxRate);
                        } else {
                            var to = totalcast + parseFloat(data.cost) + parseFloat(data.taxRate);
                        }

                        $('.totalCast1').text('$' + to);
                        $('.stncity').html('<small> delivering from ' + data.storeCity + ',' + data.storeName + '</small>')
                        $('.estimate_del').html('<small> Estimated Delivery ,' + data.Estimated_Delivery_Time + '</small>')
                        $('.estimated_del').val(data.Estimated_Delivery_Time);

                        $('.dlCast').css("display", "block!important");
                        $('.taxR').html(`<h6 class="my-0">Tax
                                    </h6>`);
                        $('.taxRa').html('$' + data.taxRate);
                        $('.amountTotal').val(to);
                        $('.deleveryCast1').val(de);
                        $('.shippingCharge').val(data.taxRate);
                        $('.pk_locations').val(data.pk_location)

                        if ($('input[name="choise_details"]:checked').val() == 'store') {
                            console.log(1);
                            $('.DeliveryChargeDiv').hide();
                        }

                    },
                    complete  : function () {
                        $('.loder').text("");
                    },
                })
                //end code
            } else {
                // this code
                $.ajax({
                    url       : "{{ url('other-checkoutss') }}",
                    type      : 'post',
                    dataType  : 'json',
                    data      : {
                        '_token': '{{ csrf_token() }}',
                        city    : city,
                        address : address
                    },
                    beforeSend: function () {
                        $('.loade').html(`<div class="loader1"></div>
                            `);

                    },
                    success   : function (data) {
                        var totalcast = parseFloat($('.totalCast').val());
                        var de = data.cost;
                        $('.deleveryCast').text('$' + de);

                        if ($('input[name="choise_details"]:checked').val() == 'store') {
                            de = 0;
                            var to = totalcast + parseFloat(data.taxRate);
                        } else {
                            var to = totalcast + parseFloat(data.cost) + parseFloat(data.taxRate);
                        }
                        $('.totalCast1').text('$' + to);
                        $('.stncity').html('<small> delivering from ' + data.storeCity + ',' + data.storeName + '</small>')
                        $('.estimate_del').html('<small> Estimated Delivery ,' + data.Estimated_Delivery_Time + '</small>')
                        $('.estimated_del').val(data.Estimated_Delivery_Time);
                        $('.dlCast').css("display", "block!important");
                        $('.taxR').html(`<h6 class="my-0">Tax
                                    </h6>`);
                        $('.taxRa').html('$' + data.taxRate);
                        $('.amountTotal').val(to);
                        $('.deleveryCast1').val(de);
                        $('.shippingCharge').val(data.taxRate);
                        $('.pk_locations').val(data.pk_location)

                        if ($('input[name="choise_details"]:checked').val() == 'store') {
                            console.log(2);
                            $('.DeliveryChargeDiv').hide();
                        }

                    },
                    complete  : function () {
                        $('.loder').text("");
                    },
                })
                //end code
            }
        }

        function setNewAddress(value) {
            var city = $('#billing_city').val();
            var address = $('#billing_address').val();
            if (city) {
                shippingCity(city, address);
            }

            $('.billing').show();
            $('.store').hide();
            $('.full-address-div').hide();
            if (value == true) {
                $('.full-address-div').show();
                $('.copyAdrs').addClass('d-none');
            }
        }

        function setExisting(value) {
            var aadd = $('#existing_address_id').val();
            if (aadd) {
                getAddreessById(aadd);
            }

            $('.full-address-div').hide();
            $('.billing').hide();
            $('.store').hide();
        }

        function myFun(item) {
            let value = $(item).data('text');

            console.log('myFun -> ', value);

            if (value == 'Delivery') {
                // $('.billing').show();
                $('.store').hide();

                var order_add = $('.abcde:selected').data('city');
                if (order_add) {
                    shippingCity('', order_add);
                } else {
                    var city = ($("#billing_city").val());
                    var address = ($("#billing_address").val());
                    if (city) {
                        shippingCity(city, address);
                    }
                }
                $('.DeliveryChargeDiv').show();
            }

            if (value == 'Store Pickup') {
                var order_add = $('.abcde:selected').data('city');
                var adds = $('.abcde:selected').data('address');
                var adds1 = $('.abcde:selected').data('address-1');
                var zip = $('.abcde:selected').data('zip');
                var storeaddress = $('.storeaddress').val();
                var storeaddress1 = $('.storeaddress1').val();
                var storezip = $('.storezip').val();
                var storecity = $('.storecity').val();
                $('.billing').hide();
                $('.store').show();
                if (order_add) {
                    var city = order_add;
                    var address = adds;
                    var address_1 = adds1;
                    var postal_code = zip;

                } else if (storeaddress) {
                    var city = storeaddress;
                    var address = storeaddress1;
                    var address_1 = storezip;
                    var postal_code = storecity;
                } else {
                    var city = ($("#billing_city").val());
                    var address = ($("#billing_address").val());
                    var address_1 = ($("#billing_address_1").val());
                    var postal_code = ($("#billing_zip").val());

                }
                if (city) {
                    shippingCity(city, address);
                }
                $.ajax({
                    url       : "{{ url('other-checkouts') }}",
                    type      : 'post',
                    dataType  : 'json',
                    data      : {
                        '_token'   : '{{ csrf_token() }}',
                        city       : city,
                        address    : address,
                        address_1  : address_1,
                        postal_code: postal_code,
                    },
                    beforeSend: function () {
                        $('.loder').html(`<div class="loader"></div>
                    `);

                    },
                    success   : function (data) {
                        $('.abcd').html(data.html);
                        $('.DeliveryChargeDiv').hide();
                        $('.estimate_del').html('');
                        $('.deleveryCast').html('$' + 0);
                        $('.store_select').attr('value', 'existing');
                    },
                    complete  : function () {
                        $('.loder').text("");
                    },

                });


            }
        }
    </script>

    <script type="text/javascript">
        function RestrictCommaSemicolon(e) {
            var theEvent = e || window.event;
            var key = theEvent.keyCode || theEvent.which;
            key = String.fromCharCode(key);
            var regex = /[^,;]+$/;
            if (!regex.test(key)) {
                theEvent.returnValue = false;
                if (theEvent.preventDefault) {
                    theEvent.preventDefault();
                }
            }
        }
    </script>

    <script type="text/javascript">
        var addresno = 0;
        var autocomplete;
        var autocomplete2;
        var autocomplete3;
        var autocomplete4;
        var componentForm = {
            street_number: 'short_name',
            //route: 'long_name',
            locality                   : 'long_name',
            administrative_area_level_1: 'short_name',
            country                    : 'long_name',
            postal_code                : 'short_name'
        };

        function initAutocomplete() {
            autocomplete = new google.maps.places.Autocomplete(
                /** @type {!HTMLInputElement} */
                (document.getElementById('address')), {
                    componentRestrictions: {
                        country: ["us"]
                    },
                    fields               : ["address_components", "geometry"],
                    types                : ['geocode']
                }
            );
            autocomplete.addListener('place_changed', function () {
                fillInAddress.call(autocomplete, 1)
                var city = ($("#locality").val());
                var address = ($("#address").val());
                if (city) {
                    console.log('city')
                    shippingCity(city, address);
                }
            });

            autocomplete2 = new google.maps.places.Autocomplete(
                /** @type {!HTMLInputElement} */
                (document.getElementById('billing_address')), {
                    componentRestrictions: {
                        country: ["us"]
                    },
                    fields               : ["address_components", "geometry"],
                    types                : ['geocode']
                }
            );
            autocomplete2.addListener('place_changed', function () {
                fillInAddress.call(autocomplete2, 2)
            });

            autocomplete3 = new google.maps.places.Autocomplete(
                /** @type {!HTMLInputElement} */
                (document.getElementById('shipping_address')), {
                    componentRestrictions: {
                        country: ["us"]
                    },
                    fields               : ["address_components", "geometry"],
                    types                : ['geocode']
                }
            );
            autocomplete3.addListener('place_changed', function () {
                fillInAddress.call(autocomplete3, 3)
            });

            autocomplete4 = new google.maps.places.Autocomplete(
                /** @type {!HTMLInputElement} */
                (document.getElementById('primary_address')), {
                    componentRestrictions: {
                        country: ["us"]
                    },
                    fields               : ["address_components", "geometry"],
                    types                : ['geocode']
                }
            );
            autocomplete4.addListener('place_changed', function () {
                fillInAddress.call(autocomplete4, 4)
            });
        }

        function fillInAddress(v1) {
            // Get the place details from the autocomplete object.
            if (v1 == 1) {
                var place = autocomplete.getPlace();
            }
            if (v1 == 2) {
                var place = autocomplete2.getPlace();
            }
            if (v1 == 3) {
                var place = autocomplete3.getPlace();
            }

            if (v1 == 4) {
                var place = autocomplete4.getPlace();
            }

            var new_address = '';
            for (var i = 0; i < place.address_components.length; i++) {
                var addressType = place.address_components[i].types[0];

                if (addressType == 'street_number') {
                    new_address += place.address_components[i]['short_name'];
                    if (v1 == 1) {
                        document.getElementById("address").value = new_address;
                    }
                    if (v1 == 2) {
                        document.getElementById("billing_address").value = new_address;
                    }
                    if (v1 == 3) {
                        document.getElementById("shipping_address").value = new_address;
                    }

                    if (v1 == 4) {
                        $('#primary_address').val(new_address);
                    }
                }
                if (addressType == 'route') {
                    if (new_address)
                        new_address += " " + place.address_components[i]['long_name'];
                    else
                        new_address += place.address_components[i]['long_name'];

                    if (v1 == 1) {
                        document.getElementById("address").value = new_address;
                    }
                    if (v1 == 2) {
                        document.getElementById("billing_address").value = new_address;
                    }
                    if (v1 == 3) {
                        document.getElementById("shipping_address").value = new_address;
                    }

                    if (v1 == 4) {
                        $('#primary_address').val(new_address);
                    }
                } else if (new_address == '' && addressType == 'locality') {
                    new_address += place.address_components[i]['long_name'];

                    if (v1 == 1) {
                        document.getElementById("address").value = new_address;
                    }
                    if (v1 == 2) {
                        document.getElementById("billing_address").value = new_address;
                    }
                    if (v1 == 3) {
                        document.getElementById("shipping_address").value = new_address;
                    }

                    if (v1 == 4) {
                        $('#primary_address').val(new_address);
                    }

                }

                if (componentForm[addressType]) {
                    var val = place.address_components[i][componentForm[addressType]];
                    if (v1 == 1) {
                        if (addressType == 'locality') {
                            $('#locality').val(val);
                        }
                        if (addressType == 'administrative_area_level_1') {
                            $('#administrative_area_level_1').val(val);
                        }
                        /*if (addressType == 'country') {
                            $('#country').val(val);
                        }*/
                        if (addressType == 'postal_code') {
                            $('#postal_code').val(val);
                        }
                    }

                    if (v1 == 2) {
                        if (addressType == 'locality') {
                            $('#billing_city').val(val);
                            addressUpdate(val, 'billing_city', val);
                        }
                        if (addressType == 'administrative_area_level_1') {
                            $('#billing_state_name').val(val);
                        }
                        /*if (addressType == 'country') {
                            $('#billing_country_name').val(val);
                        }*/
                        if (addressType == 'postal_code') {
                            $('#billing_zip').val(val);
                        }
                    }

                    if (v1 == 3) {
                        if (addressType == 'locality') {
                            $('#shipping_city').val(val);
                            var address = $('#shipping_address').val();
                            shippingCity(val, address);
                        }
                        if (addressType == 'administrative_area_level_1') {
                            $('#shipping_state_name').val(val);
                        }
                        /*if (addressType == 'country') {
                            $('#shipping_country_name').val(val);
                        }*/
                        if (addressType == 'postal_code') {
                            $('#shipping_zip').val(val);
                        }
                    }

                    if (v1 == 4) {
                        if (addressType == 'locality') {
                            $('#primary_city').val(val);
                        }
                        if (addressType == 'administrative_area_level_1') {
                            $('#primary_state_name').val(val);
                        }
                        /*if (addressType == 'country') {
                            $('#primary_country').val(val);
                        }*/
                        if (addressType == 'postal_code') {
                            $('#primary_postal_code').val(val);
                        }
                    }

                }

            }


        }
    </script>

    <script type="text/javascript">
        $(document).ready(function () {
            function cartItemShipAddrCharges(address, city, id) {
                $('.couponApply').val('');
                $('.disc1').html('');
                $('.disc').html('');
                var totalcast = parseFloat($('.totalCast').val());
                $('.amountTotal').val(totalcast);
                var to = totalcast;
                $('.totalCast1').html('$' + to);
                $('.discountCharge').val('');
                let deliveryChargeSection = $('#cart-item-delivery-charges');
                let isSameAsBilling = !!$(`#is_same_as_billing${id}`).val();
                console.log('cartItemShipAddrCharges isSameAsBilling -> ', isSameAsBilling)

                $.ajax({
                    url     : "{{ url('other-checkout-ship-info') }}",
                    type    : 'POST',
                    dataType: 'json',
                    data    : {
                        '_token': '{{ csrf_token() }}',
                        city    : city,
                        address : address
                    },
                    success : function (response) {
                        console.log('cartItemShipAddrCharges response -> ', response)
                        var totalcast = parseFloat($('.totalCast').val());
                        var taxRate = $('#tax_rate').val() || response.taxRate;

                        var deliveryCharge = response.delivery_charge;
                        console.log(totalcast)
                        console.log(taxRate)
                        console.log(deliveryCharge)

                        $(`#delivery_charge${id}`).val(deliveryCharge);
                        $(`#store_city${id}`).val(response.storeCity);
                        $(`#store_name${id}`).val(response.storeName);
                        $(`#estimated_del${id}`).val(response.estimated_delivery_time);

                        if ($('input[name="choise_details"]:checked').val() == 'store') {
                            var to = totalcast + parseFloat(taxRate);
                        } else {
                            var to = totalcast + parseFloat(deliveryCharge) + parseFloat(taxRate);
                        }
                        let cartItemName = $(`#cart-item-name${id}`).text();
                        let chargeHtml = `<li class="list-group-item d-flex justify-content-between lh-condensed" id="delivery-charge-item${id}">
                            <h6 class="my-0">
                                Delivery Charge For <strong>${cartItemName}</strong>
                                <br>
                                <small>
                                    delivering from ${response.storeCity},${response.storeName}
                                </small>
                                <br>
                                <small>Estimdated Delivery, ${response.estimated_delivery_time}</small>
                            </h6>

                            <span class="text-muted">${deliveryCharge}</span>
                    </li>`;
                        console.log('cartItemShipAddrCharges to -> ', chargeHtml)
                        $('.totalCast1').text('$' + to);
                        $('.amountTotal').val(to);
                        $('.totalCast').val(totalcast + parseFloat(deliveryCharge));
                        $(`#delivery-charge-item${id}`).remove();
                        deliveryChargeSection.append(chargeHtml);
                        deliveryChargeSection.show();
                        cartItemAddrIsSame();
                        if (!$('#tax_rate').val()) {
                            $('.taxR').html(`<h6 class="my-0">Tax
                                    </h6>`);
                            $('.taxRa').html('$' + response.taxRate);
                            $('#tax_rate').val(response.taxRate);
                        }

                    }
                })

            }

            function cartItemAddrIsSame() {
                const itemAddresses = $('.item-address-checkbox');
                // check all is checked or not if all is not checked then delivery charge hide else show
                let allChecked = true;
                itemAddresses.each(function () {
                    allChecked = $(this).is(':checked');
                });
                console.log('cartItemAddrIsSame allChecked -> ', allChecked)
                if (allChecked) {
                    $('.DeliveryChargeDiv').show();
                    $('#pickup-date').attr('required', 'required');
                    $('#pickup-date-div').show();
                } else {
                    $('.DeliveryChargeDiv').hide();
                    $('#pickup-date').removeAttr('required');
                    $('#pickup-date').val('');
                    $('#pickup-date-div').hide();
                }
                return allChecked;
            }

            $('.item-address-checkbox').on('change', function () {
                let isChecked = $(this).is(':checked');
                let id = $(this).data('id');
                let addressInput = document.getElementById('billing_address' + id);
                $(`#is_same_as_billing${id}`).val(isChecked ? 1 : 0);

                if (!isChecked) {
                    let itemAutocomplete = new google.maps.places.Autocomplete(
                        /** @type {!HTMLInputElement} */
                        (addressInput), {
                            componentRestrictions: {
                                country: ["us"]
                            },
                            fields               : ["address_components", "geometry"],
                            types                : ['geocode']
                        }
                    );

                    itemAutocomplete.addListener('place_changed', async function () {
                        await fillInItemAddress.call(itemAutocomplete, itemAutocomplete, id);
                        let city = $(`#billing_city${id}`).val();
                        let address = $(`#billing_address${id}`).val();
                        cartItemShipAddrCharges(address, city, id);
                    });
                } else {
                    $(`#delivery_charge${id}`).val(0);
                    $(`#delivery-charge-item${id}`).remove();
                }
                cartItemAddrIsSame();
            });

            @if(isset($oldData['item_address']))
            $('.item-address-checkbox').each(function () {
                let isChecked = $(this).is(':checked');
                let id = $(this).data('id');
                let addressInput = document.getElementById('billing_address' + id);
                $(`#is_same_as_billing${id}`).val(isChecked ? 1 : 0);

                if (!isChecked) {
                    let itemAutocomplete = new google.maps.places.Autocomplete(
                        /** @type {!HTMLInputElement} */
                        (addressInput), {
                            componentRestrictions: {
                                country: ["us"]
                            },
                            fields               : ["address_components", "geometry"],
                            types                : ['geocode']
                        }
                    );

                    itemAutocomplete.addListener('place_changed', async function () {
                        await fillInItemAddress.call(itemAutocomplete, itemAutocomplete, id);
                        let city = $(`#billing_city${id}`).val();
                        let address = $(`#billing_address${id}`).val();
                        cartItemShipAddrCharges(address, city, id);
                    });
                } else {
                    $(`#delivery_charge${id}`).val(0);
                    $(`#delivery-charge-item${id}`).remove();
                }
                cartItemAddrIsSame();
            });
            @endif

            function fillInItemAddress(autocomplete, id) {
                // Get the place details from the autocomplete object.
                let place = autocomplete.getPlace();

                var new_address = '';
                for (var i = 0; i < place.address_components.length; i++) {
                    var addressType = place.address_components[i].types[0];

                    if (addressType == 'street_number') {
                        new_address += place.address_components[i]['short_name'];
                        document.getElementById("billing_address" + id).value = new_address;
                    }

                    if (addressType == 'route') {
                        if (new_address)
                            new_address += " " + place.address_components[i]['long_name'];
                        else
                            new_address += place.address_components[i]['long_name'];

                        document.getElementById("billing_address" + id).value = new_address;
                    } else if (new_address == '' && addressType == 'locality') {
                        new_address += place.address_components[i]['long_name'];

                        document.getElementById("billing_address" + id).value = new_address;
                    }

                    if (componentForm[addressType]) {
                        var val = place.address_components[i][componentForm[addressType]];
                        if (addressType == 'locality') {
                            $(`#billing_city${id}`).val(val);
                        }
                        if (addressType == 'administrative_area_level_1') {
                            $(`#billing_state_name${id}`).val(val);
                        }
                        /*if (addressType == 'country') {
                            $(`#shipping_country_name${id}`).val(val);
                        }*/
                        if (addressType == 'postal_code') {
                            $(`#shipping_zip${id}`).val(val);
                        }
                    }

                }

            }
        });
    </script>

    <script type="text/javascript">
        $(document).ready(function () {
            $('.pickup-date').datepicker();
        });
    </script>
@endsection
