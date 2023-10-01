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

        .modal-backdrop.show {
            display: none !important;
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
                                <span
                                    class="text-muted">${{ number_format($details['price'] * $details['quantity'], 2) }}</span>
                            </li>
                        @endforeach
                    @endif

                    <li class="list-group-item d-flex justify-content-between">
                        <div class="subT">
                            <h6 class="my-0">
                                Subtotal
                            </h6>
                        </div>
                        <span class="text-muted loade">
                            ${{ number_format($total, 2) }}
                        </span>
                    </li>

                    @php
                        $itemAddresses = @$oldData['item_address'] ?? [];
                        $deliveryCharge = 0;
                        $duplicateItemAddresses = [];

                        foreach ($itemAddresses as $key => $itemAddress) {
                            $address = $itemAddress['shipping_address'] . ' ' . $itemAddress['shipping_address_1'] . ' ' . $itemAddress['shipping_city'] . ' ' . $itemAddress['shipping_state_name'] . ' ' . $itemAddress['shipping_zip'] . ' ' . $itemAddress['delivery_date'];
                            if ($itemAddress['same_as_billing'] == 0 && !in_array($address, $duplicateItemAddresses)) {
                                $deliveryCharge += $itemAddress['delivery_charge'];
                            }

                            $duplicateItemAddresses[$key] = $address;

                        }
                        $cartItems = session('oth_cart') ?? [];
                    @endphp

                    @if(@count(@$itemAddresses))
                        @php
                            $duplicateItemAddresses = [];
                        @endphp
                        @foreach(@$itemAddresses as $ik => $itemAddress)
                            @php
                                $address = $itemAddress['shipping_address'] . ' ' . $itemAddress['shipping_address_1'] . ' ' . $itemAddress['shipping_city'] . ' ' . $itemAddress['shipping_state_name'] . ' ' . $itemAddress['shipping_zip'] . ' ' . $itemAddress['delivery_date'];
                            @endphp

                            @if($itemAddress['same_as_billing'] == 0 && !in_array($address, $duplicateItemAddresses))
                                <li class="list-group-item d-flex justify-content-between lh-condensed delivery-charge-item"
                                    id="delivery-charge-item{{ $ik }}">
                                    <h6 class="my-0">
                                        Delivery Charge For <strong>{{ $cartItems[$ik]['name'] }}</strong>
                                        <br>
                                        <small>
                                            delivering from
                                            {{ @$itemAddress['store_city'] }}, {{ @$itemAddress['store_name'] }}
                                        </small>
                                        <br>
                                        <small>Estimdated Delivery, {{ @$itemAddress['delivery_date'] }}</small>
                                    </h6>

                                    <span class="text-muted">${{ @$itemAddress['delivery_charge'] }}</span>
                                </li>
                            @endif

                            @php
                                $duplicateItemAddresses[$ik] = $address;
                            @endphp
                        @endforeach
                    @endif

                    <li class="list-group-item d-flex justify-content-between lh-condensed dlCast"
                        id="tax-rate-section">
                        @php
                            $taxRate = old('shippingCharge', @$oldData['shippingCharge']);
                            $taxTotal = $total * $taxRate / 100;
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
                                ${{ number_format($taxTotal, 2) }}
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

                            $taxRateAmount = ($total * $taxRate) / 100;
                            $grandTotal = $total + $deliveryCharge + $taxRateAmount;

                            if (isset($oldData['discountCharge'])) {
                                $couponCharge = explode(" ", @$oldData['discountCharge']);

                                if ($couponCharge[1] === '%') {
                                    $discountedAmount             = $grandTotal * @$couponCharge[0] / 100;
                                } elseif ($coupon[0] === '$') {
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

                <div class="form-group">
                    <label>Apply Coupon (If you have)</label>
                    <input type="text" name="coupon" class="form-control couponApply" onKeyup="couponApply(this.value)"
                           value="{{ old('couponCode', @$oldData['couponCode']) }}">
                </div>

            </div>

            <div class="col-md-8 order-md-1">
                <form action="{{ route('other-checkout-preview-post') }}" method="POST" id="checkoutForm">
                    @csrf

                    <h4 class="mb-3">Ordering as Guest User</h4>
                    <label for="">
                        <a href="{{ url('guestLogin') }}">Login</a> if you are already a user , or
                        <a href="{{ url('guestRegister') }}">Register</a> as New User!
                    </label>

                    {{-- User Info Section --}}
                    <div class="row">
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
                    </div>

                    <hr class="mb-4">

                    {{-- Delivery Options --}}
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
                                <input type="radio" name="choise_details" class="choise-details"
                                       value="{{ $deliveryOption->pk_delivery_or_pickup }}"
                                       data-text="{{ $deliveryOption->delivery_or_pickup }}"
                                    {{ $choiseDetailsChecked }}>
                                <span class="mr-4">
                                    {{ Str::title($deliveryOption->delivery_or_pickup) }}
                                </span>
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

                    <hr class="mb-4">

                    {{-- Shipping Address --}}
                    <div class="billing">
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
                                            <label for="billing_address{{ $id }}">Address</label>
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

                                    @if($loop->first)
                                        <input type="hidden" id="is_same_as_billing{{ $id }}"
                                               name="item_address[{{ $id }}][same_as_billing]" value="0">
                                    @else
                                        <input type="hidden" id="is_same_as_billing{{ $id }}"
                                               name="item_address[{{ $id }}][same_as_billing]" value="{{ old('item_address') &&
                                                        !empty(old('item_address.'.$id.'.same_as_billing')) ?
                                                        old('item_address.'.$id.'.same_as_billing') : @$addressItems[$id]['same_as_billing'] }}">
                                    @endif

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
                                                        old('item_address.'.$id.'.store_city') ?? 1 : @$addressItems[$id]['store_city'] ?? 1 }}">

                                    <input type="hidden" id="store_name{{ $id }}"
                                           name="item_address[{{ $id }}][store_name]"
                                           value="{{ old('item_address') &&
                                                        !empty(old('item_address.'.$id.'.store_name')) ?
                                                        old('item_address.'.$id.'.store_name') : @$addressItems[$id]['store_name'] }}">

                                    <input type="hidden" id="shipping-lat{{ $id }}"
                                           name="item_address[{{ $id }}][shipping_lat]"
                                           value="{{ old('item_address') &&
                                                        !empty(old('item_address.'.$id.'.shipping_lat')) ?
                                                        old('item_address.'.$id.'.shipping_lat') : @$addressItems[$id]['shipping_lat'] }}">

                                    <input type="hidden" id="shipping-lng{{ $id }}"
                                           name="item_address[{{ $id }}][shipping_lng]"
                                           value="{{ old('item_address') &&
                                                        !empty(old('item_address.'.$id.'.shipping_lng')) ?
                                                        old('item_address.'.$id.'.shipping_lng') : @$addressItems[$id]['shipping_lng'] }}">
                                </div>
                            @endforeach
                        @endif

                        @php
                            $get_address_type = old('address_type', @$oldData['address_type']);
                        @endphp
                    </div>

                    {{-- Store Pickup Address Selection --}}
                    <div class="store"
                         style="{{ $oldDeliveryOption && $oldDeliveryOption->delivery_or_pickup == 'Store Pickup' ? '' : 'display:none;' }}">
                        <div class="loder"></div>

                        <div class="row mt-3 abcd">
                        </div>
                    </div>

                    <hr class="mb-4">

                    <input type="hidden" class="amountTotal" id="amount" name="amount"
                           value="{{ old('amount', @$oldData['amount'] ?? $total) }}">

                    <input type="hidden" class="deleveryCast1" name="deleveryCast1"
                           value="{{ old('deleveryCast1', @$oldData['deleveryCast1'] ?? @$deliveryCharge) }}">

                    <input type="hidden" class="shippingCharge" id="tax_rate" name="shippingCharge"
                           value="{{ old('shippingCharge', @$oldData['shippingCharge']) }}">

                    <input type="hidden" class="discountCharge" name="discountCharge"
                           value="{{ old('discountCharge', @$oldData['discountCharge']) }}">

                    <input type="hidden" id="couponCode" name="couponCode"
                           value="{{ old('couponCode', @$oldData['couponCode']) }}">

                    <input type="hidden" class="pk_locations" name="pk_locations"
                           value="{{ old('pk_locations', @$oldData['pk_locations']) }}">

                    <input type="hidden" name="pk_location_times" id="pk_location_times"
                           value="{{ old('pk_location_times', @$oldData['pk_location_times']) }}">

                    <input type="hidden" class="estimated_del" name="estimated_del"
                           value="{{ old('estimated_del', @$oldData['estimated_del']) }}">

                    <button class="btn btn-primary btn-lg btn-block mb-5" type="submit" id="previewBtn">
                        Continue to Review
                    </button>
                </form>

            </div>
        </div>
    </div>

    <script type="text/javascript">
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
                        $('.totalCast1').html('$' + Number(to).toFixed(2));
                        $('.disc1').html(`<h6 class="my-0">Discount (-)
                                     </h6>`);

                        $('.disc').html('$' + data[0]);
                        $('.discountCharge').val('$' + ' ' + data[0]);
                    }
                    if (data[1] == 'percent') {
                        var to = totalcast - (totalcast * data[0] / 100).toFixed(2);
                        $('.totalCast1').html('$' + Number(to).toFixed(2));
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

        // Get old pickup data for initialize
        const oldPickupZip = '{{ old('pickup_zip', @$oldData['pickup_zip']) }}';
        const oldPickupStoreId = '{{ old('pk_locations', @$oldData['pk_locations']) }}';
        const oldPickupStoreTimeId = '{{ old('pk_location_times', @$oldData['pk_location_times']) }}';

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
                    $('.loder').html(`<div class="loader"></div>`);
                },
                success   : function (data) {
                    console.log(data)
                    $('.abcd').html(data.html);
                    $('.delivery-charge-item').remove();
                    $('.estimate_del').html('');
                    $('.deleveryCast').html('$' + 0);
                    $('.deleveryCast1').val('');
                    $('.store_select').attr('value', 'existing');

                    if (oldPickupStoreId) {
                        $(`#pickup-store-checkbox-${oldPickupStoreId}`).prop('checked', true);
                        $(`#pickup-store-checkbox-${oldPickupStoreId}`).trigger('change');
                    }

                    if (oldPickupStoreTimeId) {
                        $(`#pickup-store-time-checkbox-${oldPickupStoreTimeId}`).prop('checked', true);
                        $(`#pickup-store-time-checkbox-${oldPickupStoreTimeId}`).trigger('change');
                    }
                },
                complete  : function () {
                    $('.loder').html("");
                },
                error     : function (data) {
                    console.log('error -> ', data);
                    $('#pickup-zip-msg').show();
                }

            })
        }

        function initPickupAddressIfOldData() {
            if (oldPickupZip) {
                getLatLngFromPickupZipCode(oldPickupZip);
            }
        }

        $(document).ready(function () {
            $('#pickup-zip').on('input', function () {
                let zipCode = $(this).val();
                if (!zipCode) {
                    return false;
                }

                getLatLngFromPickupZipCode(zipCode);
            });

            $(document).on('change', '.pickup-store-checkbox', function () {
                let item = $(this);
                let totalcast = parseFloat($('.totalCast').val());
                let taxRate = item.data('taxrate');
                let pkLocation = item.data('storeid');

                if (pkLocation) {
                    $('.selectTimeItem').hide();
                    $('.pk_locations').val(pkLocation);
                    $(`#selectTimeItem${pkLocation}`).show();
                }

                let taxTotal = Number(taxRate) * Number(totalcast) / 100;
                $('#tax_rate').val(taxRate);
                $('.taxR').html(`<h6 class="my-0">Tax</h6>`);
                $('.taxRa').html('$' + Number(taxTotal).toFixed(2));
                let to = totalcast + Number(taxTotal);
                $('.totalCast1').text('$' + Number(to).toFixed(2));
                $('.amountTotal').val(to);
            });

            $(document).on('change', '.pickup-store-time-checkbox', function () {
                let item = $(this);
                console.log('Store Time ID -> ', item.data('storetimeid'));
                let pkLocationTime = item.data('storetimeid');

                if (pkLocationTime) {
                    $('#pk_location_times').val(pkLocationTime);
                }
            });

            initPickupAddressIfOldData();
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
        $(document).ready(function () {
            // Google Autocomplete
            var componentForm = {
                street_number: 'short_name',
                //route: 'long_name',
                locality                   : 'long_name',
                administrative_area_level_1: 'short_name',
                country                    : 'long_name',
                postal_code                : 'short_name'
            };

            var duplicateAddresses = [];

            function initDuplicateAddresses() {
                const itemAddresses = $('.item-addr');
                itemAddresses.each(function () {
                    let id = $(this).data('id');
                    let address = $(`#billing_address${id}`).val();
                    let city = $(`#billing_city${id}`).val();
                    let zip = $(`#shipping_zip${id}`).val();
                    let date = $(`#delivery-date${id}`).val();
                    let newAddress = address + ', ' + city + ', ' + zip + ', ' + date;
                    duplicateAddresses.push(newAddress);
                });
            }

            initDuplicateAddresses();

            function cartItemShipAddrCharges(address, city, id) {
                $('.couponApply').val('');
                $('#couponCode').val('');
                $('.disc1').html('');
                $('.disc').html('');
                var totalcast = parseFloat($('.totalCast').val());
                $('.amountTotal').val(totalcast);
                var to = totalcast;
                $('.totalCast1').html('$' + Number(to).toFixed(2));
                $('.discountCharge').val('');

                let zip = $(`#shipping_zip${id}`).val();
                let date = $(`#delivery-date${id}`).val();

                $newAddress = address + ', ' + city + ', ' + zip + ', ' + date;

                if (!duplicateAddresses.includes($newAddress)) {
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
                                var taxTotal = Number(taxRate) * Number(totalcast) / 100;
                                var to = totalcast + taxTotal;
                            } else {
                                var taxTotal = Number(taxRate) * Number(totalcast) / 100;
                                var to = totalcast + parseFloat(deliveryCharge) + parseFloat(taxTotal);
                            }
                            let cartItemName = $(`#cart-item-name${id}`).text();
                            let chargeHtml = `<li class="list-group-item d-flex justify-content-between lh-condensed delivery-charge-item"
                                            id="delivery-charge-item${id}">
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
                            $('.totalCast1').text('$' + Number(to).toFixed(2));
                            $('.amountTotal').val(to);
                            $(`#delivery-charge-item${id}`).remove();

                            $(chargeHtml).insertBefore('#tax-rate-section');

                            cartItemAddrIsSame();
                            if (!$('#tax_rate').val()) {
                                $('.taxR').html(`<h6 class="my-0">Tax
                                    </h6>`);
                                $('.taxRa').html('$' + Number(taxTotal).toFixed(2));
                                $('#tax_rate').val(response.taxRate);
                            }
                        }
                    })
                }

                duplicateAddresses.push($newAddress);
            }

            function cartItemAddrIsSame() {
                const itemAddresses = $('.item-address-checkbox');
                // check all is checked or not if all is not checked then delivery charge hide else show
                let allChecked = true;
                itemAddresses.each(function () {
                    allChecked = $(this).is(':checked');
                });

                let checkedItem = $('input[name="choise_details"]:checked');
                let value = checkedItem.data('text');

                if (value == 'Delivery') {
                    if (allChecked) {
                        $('#pickup-date').removeAttr('required');
                        $('#pickup-date').val('');
                        $('#pickup-date-div').hide();
                    } else {
                        $('#pickup-date').removeAttr('required');
                        $('#pickup-date').val('');
                        $('#pickup-date-div').hide();
                    }
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
                        let city = $(`#billing_city${id}`).val();
                        let address = $(`#billing_address${id}`).val();
                        cartItemShipAddrCharges(address, city, id);
                        fillAllItemAddrFromFirstItem();
                    });
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
                    if (!isSameChecked && itemId != firstItemId) {
                        $(`#shipping_full_name${itemId}`).val(firstAddrName);
                        $(`#shipping_phone${itemId}`).val(firstAddrPhone);
                        $(`#billing_address${itemId}`).val(firstAddr);
                        $(`#billing_address_1${itemId}`).val(firstAddr1);
                        $(`#billing_city${itemId}`).val(firstCity);
                        $(`#billing_state_name${itemId}`).val(firstState);
                        $(`#shipping_zip${itemId}`).val(firstZip);
                        $(`#delivery-date${itemId}`).val(firstDelDate);
                        $(`#delivery_charge${itemId}`).val(firstDelCharge);
                    }
                });

            }

            function itemAddrInit() {
                $('.item-address-checkbox').each(function () {
                    console.log('item-address-checkbox each')
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
                        $(`#div${id}`).show();

                    } else {
                        $(`#delivery_charge${id}`).val(0);
                        $(`#delivery-charge-item${id}`).remove();
                        $(`#div${id}`).hide();

                    }
                    cartItemAddrIsSame();
                });
            }

            function firstItemAddrInit2() {
                const firstItemAddr = $('.item-addr').first();
                let id = firstItemAddr.data('id');
                let city = $(`#billing_city${id}`).val();
                let address = $(`#billing_address${id}`).val();
                if (city && address) {
                    cartItemShipAddrCharges(address, city, id);
                    fillAllItemAddrFromFirstItem();
                }
            }

            @if(isset($oldData['item_address']))
            itemAddrInit();
            @endif

            function fillInItemAddress(autocomplete, id) {
                // Get the place details from the autocomplete object.
                let place = autocomplete.getPlace();

                if (place.geometry) {
                    $(`#shipping-lat${id}`).val(place.geometry.location.lat());
                    $(`#shipping-lng${id}`).val(place.geometry.location.lng());
                }

                var new_address = '';
                for (var i = 0; i < place.address_components.length; i++) {
                    var addressType = place.address_components[i].types[0];

                    if (addressType == 'street_number') {
                        new_address += place.address_components[i]['short_name'];
                    }

                    if (addressType == 'route') {
                        if (new_address)
                            new_address += " " + place.address_components[i]['long_name'];
                        else
                            new_address += place.address_components[i]['long_name'];

                        $(`#billing_address${id}`).val(new_address);
                    } else if (new_address == '' && addressType == 'locality') {
                        new_address += place.address_components[i]['long_name'];

                        $(`#billing_address${id}`).val(new_address);
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


            $('.choise-details').on('change', function () {
                myFun();
            });

            $('.choise-details').on('click', function () {
                myFun();
            });

            function myFun() {
                console.log('myFun')
                let checkedItem = $('input[name="choise_details"]:checked');

                let value = checkedItem.data('text');

                let totalCast = Number($('.totalCast').val());
                let taxRate = $('#tax_rate').val();

                if (value == 'Delivery') {
                    console.log(value)
                    $('.billing').show();
                    $('.store').hide();
                    $('.store').find('input[name="store_id"]').removeAttr('checked');
                    $('.store').find('input[name="store_id"]').removeAttr('required');

                    $('#pickup-date-div').hide();
                    $('#pickup-date').removeAttr('required');
                    $('#pickup-date').val('');

                    $('#pickup-zip-div').hide();
                    $('#pickup-zip').removeAttr('required');
                    $('#pickup-zip').val('');
                }

                if (value == 'Store Pickup') {
                    console.log(value)
                    $('.billing').hide();
                    $('.store').show();

                    $('.delivery-charge-item').remove();

                    $('.store').find('input[name="store_id"]').attr('required', 'required');

                    $('#pickup-date-div').show();
                    $('#pickup-date').attr('required', 'required');

                    $('#pickup-zip-div').show();
                    $('#pickup-zip').attr('required', 'required');

                    let taxTotal = Number(taxRate) * Number(totalCast) / 100;
                    let to = totalCast + Number(taxTotal);
                    $('.totalCast1').text('$' + Number(to).toFixed(2));
                    $('.amountTotal').val(to);
                }
            }

            var isOldChoiseDetails = {{ $isOldChoiseDetails ?? 0 }};
            if (isOldChoiseDetails) {
                myFun();
            }
        });
    </script>

    <script type="text/javascript">
        $(document).ready(function () {
            $('.pickup-date').datepicker({
                minDate: new Date()
            });
            $('.delivery-date').datepicker({
                minDate: new Date()
            });

            $('.delivery-date').on('change', function () {
                let date = $(this).val();
                let id = $(this).data('id');

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
