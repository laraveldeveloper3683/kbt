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

                    <hr class="mb-4">

                    @php
                        $is_existing_address = '';
                    @endphp

                    @if($deliveryOptions->count())
                        @php
                            $isOldChoiseDetails = 0;
                            if (isset($oldData['choise_details'])) {
                                $isOldChoiseDetails = 1;
                            }
                        @endphp
                        <div class="">
                            @foreach($deliveryOptions as $deliveryOption)
                                @php
                                    $choiseDetailsChecked = '';
                                    if (isset($oldData['choise_details']) && $oldData['choise_details'] == $deliveryOption->pk_delivery_or_pickup) {
                                        $choiseDetailsChecked = 'checked';
                                    }

                                    if ((!isset($oldData['choise_details']) || @$oldData['choise_details'] != $deliveryOption->pk_delivery_or_pickup) && $loop->first) {
                                        $choiseDetailsChecked = 'checked';
                                    }
                                @endphp
                                <input type="radio" name="choise_details" onClick="myFun();"
                                       value="{{ $deliveryOption->pk_delivery_or_pickup }}"
                                       data-text="{{ $deliveryOption->delivery_or_pickup }}"
                                    {{ $choiseDetailsChecked }}> {{ Str::title($deliveryOption->delivery_or_pickup) }}
                            @endforeach
                        </div>

                        <div class="form-group mt-4" id="pickup-zip-div" style="display: none;">
                            <label for="pickup-zip" class="form-label">
                                Enter Pickup Zip
                            </label>
                            <input type="text" name="pickup_zip" id="pickup-zip"
                                   class="form-control pickup-zip"
                                   placeholder="Enter pickup zip"
                                   value="{{ old('pickup_zip', @$oldData['pickup_zip']) }}">
                            <span class="invalid-feedback" role="alert" id="pickup-zip-msg"
                                  style="display: none;">
                                    <strong>Sorry, we don't have pickup point to your area!</strong>
                                </span>
                            <input type="hidden" id="pickup_zip_lat" name="pickup_zip_lat">
                            <input type="hidden" id="pickup_zip_lng" name="pickup_zip_lng">
                        </div>

                        <div class="form-group mt-4" id="pickup-date-div" style="display: none;">
                            <label for="pickup-date" class="form-label">
                                Select Pickup Date
                            </label>
                            <input type="text" name="pickup_date" id="pickup-date"
                                   class="form-control pickup-date @error('pickup_date') is-invalid @enderror"
                                   placeholder="Enter pickup date"
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

                    <hr>


                    <div class="billing full-address-div">

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

                                @if(!$loop->first)
                                    <label for="checkbox{{ $id }}">
                                        <input type="checkbox" id="checkbox{{ $id }}" class="item-address-checkbox"
                                               data-id="{{ $id }}"
                                            {{ old('item_address.'.$id.'.same_as_billing', @$addressItems[$id]['same_as_billing'] ?? 1) ? 'checked' : '' }}>
                                        Use same as First Item for this item
                                    </label>
                                @endif

                                <div id="div{{ $id }}" data-id="{{ $id }}" class="item-addr"
                                     style="{{ !$loop->first && old('item_address.'.$id.'.same_as_billing', @$addressItems[$id]['same_as_billing'] ?? 1) ? 'display:none;' : '' }}">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="manage_shipping_full_name{{ $id }}">Name</label>
                                            <input type="text" class="form-control" id="shipping_full_name{{ $id }}"
                                                   name="item_address[{{ $id }}][shipping_full_name]"
                                                   value="{{ old('item_address') &&
                                                        !empty(old('item_address.'.$id.'.shipping_full_name')) ?
                                                        old('item_address.'.$id.'.shipping_full_name') : @$addressItems[$id]['shipping_full_name'] ?? @$user_data->first_name . ' ' . @$user_data->last_name }}">
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
                                                        old('item_address.'.$id.'.shipping_phone') : @$addressItems[$id]['shipping_phone'] ?? @$billingAddress->customer->office_phone }}">
                                            @error('item_address.'.$id.'.shipping_phone')
                                            <span class="invalid-feedback d-block" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                            @enderror
                                        </div>

                                    </div>

                                    <div class="row">
                                        <div class="col-md-4 mb-3">
                                            <label for="billing_address{{ $id }}">Address</label>
                                            <input type="text" class="form-control"
                                                   id="billing_address{{ $id }}"
                                                   name="item_address[{{ $id }}][shipping_address]"
                                                   value="{{ old('item_address') &&
                                                        !empty(old('item_address.'.$id.'.shipping_address')) ?
                                                        old('item_address.'.$id.'.shipping_address') : @$addressItems[$id]['shipping_address'] ?? @$billingAddress->address }}">
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
                                                        old('item_address.'.$id.'.shipping_address_1') : @$addressItems[$id]['shipping_address_1'] ?? @$billingAddress->address_1 }}">
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
                                                        old('item_address.'.$id.'.shipping_city') : @$addressItems[$id]['shipping_city'] ?? @$billingAddress->city }}">

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
                                                        old('item_address.'.$id.'.shipping_state_name') : @$addressItems[$id]['shipping_state_name'] ?? @$billingAddress->state->state_code }}">
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
                                                        old('item_address.'.$id.'.shipping_zip') : @$addressItems[$id]['shipping_zip'] ?? @$billingAddress->zip }}">
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
                                                <label for="delivery-date{{ $id }}" class="form-label">
                                                    Select Delivery Date
                                                </label>
                                                <input type="text" name="item_address[{{ $id }}][delivery_date]"
                                                       id="delivery-date{{ $id }}"
                                                       data-id="{{ $id }}"
                                                       class="form-control delivery-date"
                                                       placeholder="Enter delivery date"
                                                       value="{{ old('item_address') &&
                                                        !empty(old('item_address.'.$id.'.delivery_date')) ?
                                                        old('item_address.'.$id.'.delivery_date') : @$addressItems[$id]['delivery_date'] }}">
                                                @error('item_address.'.$id.'.delivery_date')
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

                    @if(!count($kbt_address) || !$billingAddress)
                        <strong>
                            Enter location details and choose Store Pickup to see store list
                        </strong>
                    @endif

                    @if(!count($kbt_address) || !$billingAddress)
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

                    @if(count($kbt_address))
                        <div class="col-md-12 mb-3 pl-0">
                            <h4 class="mb-3">Billing Address</h4>
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
                    @endif

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

    <script type="text/javascript">
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

                    if ($('input[name="choise_details"]:checked').data('text') == 'Store Pickup') {
                        var to = totalcast + parseFloat(data.taxRate);
                    } else {
                        var to = totalcast + parseFloat(data.cost) + parseFloat(data.taxRate);
                    }
                    $('.totalCast1').text('$' + to);
                    $('.stncity').html('<small> delivering from ' + data.storeCity + ',' + data.storeName + '</small>')
                    // $('.estimate_del').html('<small> Estimated Delivery ,' + data.Estimated_Delivery_Time + '</small>')
                    $('.estimated_del').val(data.Estimated_Delivery_Time);

                    $('.dlCast').css("display", "block!important");
                    $('.taxR').html(`<h6 class="my-0">Tax
                                    </h6>`);
                    $('.taxRa').html('$' + data.taxRate);
                    $('.amountTotal').val(to);
                    $('.deleveryCast1').val(de);
                    $('.shippingCharge').val(data.taxRate);
                    $('.pk_locations').val(data.pk_location)

                    if ($('input[name="choise_details"]:checked').data('text') == 'Store Pickup') {
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
                        if ($('input[name="choise_details"]:checked').data('text') == 'Store Pickup') {
                            de = 0;
                            var to = totalcast + parseFloat(data.taxRate);
                        } else {
                            var to = totalcast + parseFloat(data.cost) + parseFloat(data.taxRate);
                        }

                        $('.totalCast1').text('$' + to);
                        $('.stncity').html('<small> delivering from ' + data.storeCity + ',' + data.storeName + '</small>')
                        // $('.estimate_del').html('<small> Estimated Delivery ,' + data.Estimated_Delivery_Time + '</small>')
                        $('.estimated_del').val(data.Estimated_Delivery_Time);

                        $('.dlCast').css("display", "block!important");
                        $('.taxR').html(`<h6 class="my-0">Tax
                                    </h6>`);
                        $('.taxRa').html('$' + data.taxRate);
                        $('.amountTotal').val(to);
                        $('.deleveryCast1').val(de);
                        $('.shippingCharge').val(data.taxRate);
                        $('.pk_locations').val(data.pk_location)

                        if ($('input[name="choise_details"]:checked').data('text') == 'Store Pickup') {
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

                        if ($('input[name="choise_details"]:checked').data('text') == 'Store Pickup') {
                            de = 0;
                            var to = totalcast + parseFloat(data.taxRate);
                        } else {
                            var to = totalcast + parseFloat(data.cost) + parseFloat(data.taxRate);
                        }
                        $('.totalCast1').text('$' + to);
                        $('.stncity').html('<small> delivering from ' + data.storeCity + ',' + data.storeName + '</small>')
                        // $('.estimate_del').html('<small> Estimated Delivery ,' + data.Estimated_Delivery_Time + '</small>')
                        $('.estimated_del').val(data.Estimated_Delivery_Time);
                        $('.dlCast').css("display", "block!important");
                        $('.taxR').html(`<h6 class="my-0">Tax
                                    </h6>`);
                        $('.taxRa').html('$' + data.taxRate);
                        $('.amountTotal').val(to);
                        $('.deleveryCast1').val(de);
                        $('.shippingCharge').val(data.taxRate);
                        $('.pk_locations').val(data.pk_location)

                        if ($('input[name="choise_details"]:checked').data('text') == 'Store Pickup') {
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

        function myFun() {
            let checkedItem = $('input[name="choise_details"]:checked');

            let value = checkedItem.data('text');

            var city = ($("#billing_city").val() || $('.abcde:selected').data('city'));
            var address = ($("#billing_address").val() || $('.abcde:selected').data('address'));
            var address_1 = ($("#billing_address_1").val() || $('.abcde:selected').data('address-1'));
            var postal_code = ($("#billing_zip").val() || $('.abcde:selected').data('zip'));

            if (city) {
                shippingCity(city, address);
            }

            if (value == 'Delivery') {
                // $('.billing').show();
                $('.store').hide();
                $('.store').find('input[name="store_id"]').removeAttr('checked');
                $('.store').find('input[name="store_id"]').removeAttr('required');

                $('.DeliveryChargeDiv').show();

                $('#pickup-date-div').hide();
                $('#pickup-date').removeAttr('required');
                $('#pickup-date').val('');

                $('#pickup-zip-div').hide();
                $('#pickup-zip').removeAttr('required');
                $('#pickup-zip').val('');
            }

            if (value == 'Store Pickup') {
                $('.billing').hide();
                $('.store').show();

                $('.store').find('input[name="store_id"]').attr('required', 'required');

                $('#pickup-date-div').show();
                $('#pickup-date').attr('required', 'required');

                $('#pickup-zip-div').show();
                $('#pickup-zip').attr('required', 'required');

                /*$.ajax({
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
                        $('.deleveryCast1').val('');
                        $('.store_select').attr('value', 'existing');
                    },
                    complete  : function () {
                        $('.loder').text("");
                    },

                });*/

            }
        }

        var isOldChoiseDetails = {{ $isOldChoiseDetails ?? 0 }};
        if (isOldChoiseDetails) {
            myFun();
        }

        function getLatLngFromPickupZipCode(zipCode) {
            var geocoder = new google.maps.Geocoder();

            geocoder.geocode({'address': zipCode}, function (results, status) {
                if (status == google.maps.GeocoderStatus.OK) {
                    let lat = results[0].geometry.location.lat();
                    let lng = results[0].geometry.location.lng();

                    // check lat id and lng id is exist or not
                    if ($('#pickup_zip_lat').length) {
                        $('#pickup_zip_lat').val(lat);
                    }

                    if ($('#pickup_zip_lng').length) {
                        $('#pickup_zip_lng').val(lng);
                    }
                    $('#pickup-zip-msg').hide();

                    getPickupAddress();
                } else {
                    $('#pickup_zip_lat').val('');
                    $('#pickup_zip_lng').val('');
                    $('#pickup-zip-msg').show();
                    console.log('Geocode was not successful for the following reason: ' + status);
                }
            });
        }

        function getPickupAddress() {
            $.ajax({
                url       : "{{ url('other-checkout-pickup-address') }}",
                type      : 'POST',
                dataType  : 'json',
                data      : {
                    '_token': '{{ csrf_token() }}',
                    lat     : $('#pickup_zip_lat').val(),
                    lng     : $('#pickup_zip_lng').val(),
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
                    $('.deleveryCast1').val('');
                    $('.store_select').attr('value', 'existing');
                },
                complete  : function () {
                    $('.loder').text("");
                },

            });
        }

        $(document).ready(function () {
            $('#pickup-zip').on('input', function () {
                let zipCode = $(this).val();
                if (!zipCode) {
                    return false;
                }

                getLatLngFromPickupZipCode(zipCode);
            });
        });
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
        var primaryAutocomplete;
        var billingAutocomplete;
        var componentForm = {
            street_number: 'short_name',
            //route: 'long_name',
            locality                   : 'long_name',
            administrative_area_level_1: 'short_name',
            country                    : 'long_name',
            postal_code                : 'short_name'
        };

        // Init Primary Address Autocomplete
        function initPrimaryAutocomplete() {
            primaryAutocomplete = new google.maps.places.Autocomplete(
                /** @type {!HTMLInputElement} */
                (document.getElementById('primary_address')), {
                    componentRestrictions: {
                        country: ["us"]
                    },
                    fields               : ["address_components", "geometry"],
                    types                : ['geocode']
                }
            );
            primaryAutocomplete.addListener('place_changed', function () {
                fillInAddress.call(primaryAutocomplete, 'primary')
            });
        }

        // Init Billing Address Autocomplete
        function initBillingAutocomplete() {
            billingAutocomplete = new google.maps.places.Autocomplete(
                (document.getElementById('billing_address')), {
                    componentRestrictions: {
                        country: ["us"]
                    },
                    fields               : ["address_components", "geometry"],
                    types                : ['geocode']
                }
            );
            billingAutocomplete.addListener('place_changed', function () {
                fillInAddress.call(billingAutocomplete, 'billing')
            });
        }

        function fillInAddress(type = 'primary') {
            // Get the place details from the autocomplete object.
            if (type == 'primary') {
                var place = primaryAutocomplete.getPlace();
            }

            if (type == 'billing') {
                var place = billingAutocomplete.getPlace();
            }

            var new_address = '';
            for (var i = 0; i < place.address_components.length; i++) {
                var addressType = place.address_components[i].types[0];

                if (addressType == 'street_number') {
                    new_address += place.address_components[i]['short_name'];
                    if (type == 'primary') {
                        $('#primary_address').val(new_address);
                    }

                    if (type == 'billing') {
                        $('#billing_address').val(new_address);
                    }
                }

                if (addressType == 'route') {
                    if (new_address)
                        new_address += " " + place.address_components[i]['long_name'];
                    else
                        new_address += place.address_components[i]['long_name'];

                    if (type == 'primary') {
                        $('#primary_address').val(new_address);
                    }

                    if (type == 'billing') {
                        $('#billing_address').val(new_address);
                    }
                } else if (new_address == '' && addressType == 'locality') {
                    new_address += place.address_components[i]['long_name'];

                    if (type == 'primary') {
                        $('#primary_address').val(new_address);
                    }

                    if (type == 'billing') {
                        $('#billing_address').val(new_address);
                    }
                }

                if (componentForm[addressType]) {
                    var val = place.address_components[i][componentForm[addressType]];

                    if (type == 'primary') {
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

                    if (type == 'billing') {
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

                }

            }

        }

        $(document).ready(function () {
            // Init Primary Address Autocomplete
            if ($('#primary_address').is(':visible')) {
                initPrimaryAutocomplete();
            }

            // Init Billing Address Autocomplete
            if ($('#billing_address').is(':visible')) {
                initBillingAutocomplete();
            }
        });
    </script>

    <script type="text/javascript">
        $(document).ready(function () {
            function cartItemShipAddrCharges(address, city, id) {
                $('.couponApply').val('');
                $('#couponCode').val('');
                $('.disc1').html('');
                $('.disc').html('');
                var totalcast = parseFloat($('.totalCast').val());
                $('.amountTotal').val(totalcast);
                var to = totalcast;
                $('.totalCast1').html('$' + to);
                $('.discountCharge').val('');
                let deliveryChargeSection = $('#cart-item-delivery-charges');
                let isSameAsBilling = !!$(`#is_same_as_billing${id}`).val();

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

                        $(`#delivery_charge${id}`).val(deliveryCharge);
                        $(`#store_city${id}`).val(response.storeCity);
                        $(`#store_name${id}`).val(response.storeName);
                        $(`#estimated_del${id}`).val(response.estimated_delivery_time);

                        const firstItemAddr = $('.item-addr').first();
                        let firstItemId = firstItemAddr.data('id');

                        if (id == firstItemId) {
                            fillAllItemAddrFromFirstItem();
                        }

                        if ($('input[name="choise_details"]:checked').data('text') == 'Store Pickup') {
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
                                <small id="estimat_del${id}"></small>
                            </h6>

                            <span class="text-muted"><span>$</span>${deliveryCharge}</span>
                    </li>`;
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

                        $('.DeliveryChargeDiv').hide();

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

                if (allChecked) {
                    $('.DeliveryChargeDiv').show();

                    $('#pickup-date').removeAttr('required');
                    $('#pickup-date').val('');
                    $('#pickup-date-div').hide();
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

                const firstItemAddr = $('.item-addr').first();
                let firstItemId = firstItemAddr.data('id');
                let firstAddrName = $(`#shipping_full_name${firstItemId}`).val();
                let firstAddrPhone = $(`#shipping_phone${firstItemId}`).val();
                let firstAddr = $(`#billing_address${firstItemId}`).val();
                let firstAddr1 = $(`#billing_address_1${firstItemId}`).val();
                let firstCity = $(`#billing_city${firstItemId}`).val();
                let firstState = $(`#billing_state_name${firstItemId}`).val();
                let firstZip = $(`#shipping_zip${firstItemId}`).val();
                let firstDelDate = $(`#delivery-date${firstItemId}`).val();
                let firstDelCharge = $(`#delivery_charge${firstItemId}`).val();


                let billFullName = $(`#shipping_full_name${id}`);
                let billPhone = $(`#shipping_phone${id}`);
                let billingAddr = $(`#billing_address${id}`);
                let billingAddr1 = $(`#billing_address_1${id}`);
                let billingCity = $(`#billing_city${id}`);
                let billingState = $(`#billing_state_name${id}`);
                let billingZip = $(`#shipping_zip${id}`);
                let billingDelDate = $(`#delivery-date${id}`);
                let billingDelCharge = $(`#delivery_charge${id}`);

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
                    $(`#div${id}`).show();

                    // Reset all fields
                    billFullName.val('');
                    billPhone.val('');
                    billingAddr.val('');
                    billingAddr1.val('');
                    billingCity.val('');
                    billingState.val('');
                    billingZip.val('');
                    billingDelDate.val('');
                    billingDelCharge.val('');
                } else {
                    $(`#delivery_charge${id}`).val(0);
                    $(`#delivery-charge-item${id}`).remove();
                    $(`#div${id}`).hide();

                    // Fill all field
                    billFullName.val(firstAddrName);
                    billPhone.val(firstAddrPhone);
                    billingAddr.val(firstAddr);
                    billingAddr1.val(firstAddr1);
                    billingCity.val(firstCity);
                    billingState.val(firstState);
                    billingZip.val(firstZip);
                    billingDelDate.val(firstDelDate);
                    billingDelCharge.val(firstDelCharge);
                }
                cartItemAddrIsSame();
            });

            // first item addr autocomplete init
            function firstItemAddrInit() {
                const firstItemAddr = $('.item-addr').first();
                let id = firstItemAddr.data('id');
                let city = $(`#billing_city${id}`).val();
                let address = $(`#billing_address${id}`).val();
                if ($(`#billing_address${id}`).is(':visible')) {
                    let addressInput = document.getElementById('billing_address' + id);
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
                        city = $(`#billing_city${id}`).val();
                        address = $(`#billing_address${id}`).val();
                        cartItemShipAddrCharges(address, city, id);
                        fillAllItemAddrFromFirstItem();
                    });

                    cartItemShipAddrCharges(address, city, id);
                    fillAllItemAddrFromFirstItem();
                }

                $(`#shipping_full_name${id}`).on('change', function () {
                    fillAllItemAddrFromFirstItem();
                });

                $(`#shipping_phone${id}`).on('change', function () {
                    fillAllItemAddrFromFirstItem();
                });

                $(`#delivery-date${id}`).on('change', function () {
                    fillAllItemAddrFromFirstItem();
                });

                $(`#delivery_charge${id}`).on('change', function () {
                    fillAllItemAddrFromFirstItem();
                });
            }

            firstItemAddrInit();

            function fillAllItemAddrFromFirstItem() {
                const firstItemAddr = $('.item-addr').first();
                let firstItemId = firstItemAddr.data('id');
                let firstAddrName = $(`#shipping_full_name${firstItemId}`).val();
                let firstAddrPhone = $(`#shipping_phone${firstItemId}`).val();
                let firstAddr = $(`#billing_address${firstItemId}`).val();
                let firstAddr1 = $(`#billing_address_1${firstItemId}`).val();
                let firstCity = $(`#billing_city${firstItemId}`).val();
                let firstState = $(`#billing_state_name${firstItemId}`).val();
                let firstZip = $(`#shipping_zip${firstItemId}`).val();
                let firstDelDate = $(`#delivery-date${firstItemId}`).val();
                let firstDelCharge = $(`#delivery_charge${firstItemId}`).val();

                // select all item-addr without first item
                $('.item-addr').each(function () {
                    let itemId = $(this).data('id');
                    let isSameChecked = $(`#checkbox${itemId}`).is(':checked');
                    if (!isSameChecked || itemId == firstItemId) {
                        return;
                    }
                    $(`#shipping_full_name${itemId}`).val(firstAddrName);
                    $(`#shipping_phone${itemId}`).val(firstAddrPhone);
                    $(`#billing_address${itemId}`).val(firstAddr);
                    $(`#billing_address_1${itemId}`).val(firstAddr1);
                    $(`#billing_city${itemId}`).val(firstCity);
                    $(`#billing_state_name${itemId}`).val(firstState);
                    $(`#shipping_zip${itemId}`).val(firstZip);
                    $(`#delivery-date${itemId}`).val(firstDelDate);
                    $(`#delivery_charge${itemId}`).val(firstDelCharge);
                });

            }

            @if(isset($oldData['item_address']) || auth()->check())
            $('.item-address-checkbox').each(function () {
                let isChecked = $(this).is(':checked');
                let id = $(this).data('id');
                console.log('item-address-checkbox ID -> ', id)
                let addressInput = document.getElementById('billing_address' + id);
                $(`#is_same_as_billing${id}`).val(isChecked ? 1 : 0);

                const firstItemAddr = $('.item-addr').first();
                let firstItemId = firstItemAddr.data('id');
                let firstAddrName = $(`#shipping_full_name${firstItemId}`).val();
                let firstAddrPhone = $(`#shipping_phone${firstItemId}`).val();
                let firstAddr = $(`#billing_address${firstItemId}`).val();
                let firstAddr1 = $(`#billing_address_1${firstItemId}`).val();
                let firstCity = $(`#billing_city${firstItemId}`).val();
                let firstState = $(`#billing_state_name${firstItemId}`).val();
                let firstZip = $(`#shipping_zip${firstItemId}`).val();
                let firstDelDate = $(`#delivery-date${firstItemId}`).val();
                let firstDelCharge = $(`#delivery_charge${firstItemId}`).val();


                let billFullName = $(`#shipping_full_name${id}`);
                let billPhone = $(`#shipping_phone${id}`);
                let billingAddr = $(`#billing_address${id}`);
                let billingAddr1 = $(`#billing_address_1${id}`);
                let billingCity = $(`#billing_city${id}`);
                let billingState = $(`#billing_state_name${id}`);
                let billingZip = $(`#shipping_zip${id}`);
                let billingDelDate = $(`#delivery-date${id}`);
                let billingDelCharge = $(`#delivery_charge${id}`);

                let city = $(`#billing_city${id}`).val();
                let address = $(`#billing_address${id}`).val();

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
                        city = $(`#billing_city${id}`).val();
                        address = $(`#billing_address${id}`).val();
                        cartItemShipAddrCharges(address, city, id);
                    });
                    $(`#div${id}`).show();

                    // Reset all fields
                    billFullName.val('');
                    billPhone.val('');
                    billingAddr.val('');
                    billingAddr1.val('');
                    billingCity.val('');
                    billingState.val('');
                    billingZip.val('');
                    billingDelDate.val('');
                    billingDelCharge.val('');
                } else {
                    $(`#delivery_charge${id}`).val(0);
                    $(`#delivery-charge-item${id}`).remove();
                    $(`#div${id}`).hide();

                    // Fill all field
                    billFullName.val(firstAddrName);
                    billPhone.val(firstAddrPhone);
                    billingAddr.val(firstAddr);
                    billingAddr1.val(firstAddr1);
                    billingCity.val(firstCity);
                    billingState.val(firstState);
                    billingZip.val(firstZip);
                    billingDelDate.val(firstDelDate);
                    billingDelCharge.val(firstDelCharge);
                }
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
            $('.delivery-date').datepicker();

            $('.delivery-date').on('change', function () {
                let date = $(this).val();
                let id = $(this).data('id');

                console.log('delivery date -> ', date)
                console.log('delivery date id -> ', id)

                if (date) {
                    if (id) {
                        $(`#estimat_del${id}`).html(`<strong>Estimated Delivery - </strong>${date}`);
                    } else {
                        $('.estimate_del').html(`<small><strong>Estimated Delivery - </strong>${date}</small>`);
                    }
                } else {
                    if (id) {
                        $(`#estimat_del${id}`).html('');
                    } else {
                        $('.estimate_del').html('');
                    }
                }
            });
        });
    </script>
@endsection
