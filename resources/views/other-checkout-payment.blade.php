@extends('layouts.frontend')

@section('title', 'Checkout Preview')

@section('content')

    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="card mb-5">
                    <div class="card-header">
                        <h4 class="card-title text-center">
                            ORDER PAYMENT
                        </h4>
                    </div>
                    <form action="{{ route('other-checkout') }}" method="POST">
                        @csrf

                        <div class="card-body">
                            <div class="row justify-content-center">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="cc-name">
                                            Name on card
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" id="cc_name" name="cc_name" required
                                               class="form-control @error('cc_name') is-invalid @enderror"
                                               value="{{ old('cc_name') }}" placeholder="Enter card name">
                                        <small class="text-muted">Full name as displayed on card</small>
                                        @error('cc_name')
                                        <span class="invalid-feedback d-block" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="cc-number">
                                            Credit card number
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" id="cc_number" name="cc_number" required
                                               class="form-control @error('cc_number') is-invalid @enderror"
                                               value="{{ old('cc_number') }}" placeholder="Enter card number">
                                        @error('cc_number')
                                        <span class="invalid-feedback d-block" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                        @enderror
                                    </div>

                                    <div class="row">
                                        <div class="col-md-4 mb-3">
                                            @php
                                                $months = [
                                                        1 => 'Jan',
                                                        2 => 'Feb',
                                                        3 => 'Mar',
                                                        4 => 'Apr',
                                                        5 => 'May',
                                                        6 => 'Jun',
                                                        7 => 'Jul',
                                                        8 => 'Aug',
                                                        9 => 'Sep',
                                                        10 => 'Oct',
                                                        11 => 'Nov',
                                                        12 => 'Dec'
                                                    ];
                                            @endphp
                                            <label for="expiry_month">
                                                Expiration Date
                                                <span class="text-danger">*</span>
                                            </label>
                                            <select class="custom-select d-block w-100" id="expiry_month"
                                                    name="expiry_month" required>
                                                <option value="">Month</option>
                                                @foreach ($months as $mkey => $mval)
                                                    <option value="{!! $mkey !!}"
                                                            @if (old('expiry_month') == $mkey) selected @endif>{!! $mval !!}</option>
                                                @endforeach
                                            </select>
                                            @error('expiry_month')
                                            <span class="invalid-feedback d-block" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                            @enderror
                                        </div>

                                        <div class="col-md-4 mb-3">
                                            <label for="expiry_year">&nbsp;</label>
                                            <select class="custom-select d-block w-100" id="expiry_year"
                                                    name="expiry_year" required>
                                                <option value="">Year</option>
                                                @for ($i = date('Y'); $i <= date('Y') + 15; $i++)
                                                    <option value="{!! $i !!}"
                                                            @if (old('expiry_year') == $i) selected @endif>{!! $i !!}</option>
                                                @endfor
                                            </select>
                                            @error('expiry_year')
                                            <span class="invalid-feedback d-block" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                            @enderror
                                        </div>

                                        <div class="col-md-4 mb-3">
                                            <label for="cc-cvv">
                                                CVV
                                                <span class="text-danger">*</span>
                                            </label>
                                            <input type="text" id="cvv" name="cvv" class="form-control d-block w-100"
                                                   value="{{ old('cvv') }}" style="height: 38px !important;"
                                                   placeholder="CVV/CVC" required>
                                            @error('cvv')
                                            <span class="invalid-feedback d-block" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <a href="{{ route('other-checkout-preview') }}" class="btn btn-primary">
                                <i class="fa fa-angle-left"></i>
                                Back
                            </a>
                            <button type="submit" class="btn btn-primary float-right">
                                Confirm
                                <i class="fa fa-angle-right"></i>
                            </button>
                        </div>

                        <input type="hidden" name="first_name" value="{{ old('first_name', @$data['first_name']) }}">
                        <input type="hidden" name="last_name" value="{{ old('last_name', @$data['last_name']) }}">
                        <input type="hidden" name="username" value="{{ old('username', @$data['username']) }}">
                        <input type="hidden" name="phone" value="{{ old('phone', @$data['phone']) }}">
                        <input type="hidden" name="email" value="{{ old('email', @$data['email']) }}">
                        <input type="hidden" name="primary_address"
                               value="{{ old('primary_address', @$data['primary_address']) }}">
                        <input type="hidden" name="primary_address_1"
                               value="{{ old('primary_address_1', @$data['primary_address_1']) }}">
                        <input type="hidden" name="primary_city"
                               value="{{ old('primary_city', @$data['primary_city']) }}">
                        <input type="hidden" name="primary_state_name"
                               value="{{ old('primary_state_name', @$data['primary_state_name']) }}">
                        <input type="hidden" name="primary_zip" value="{{ old('primary_zip', @$data['primary_zip']) }}">
                        <input type="hidden" name="primary_country_name"
                               value="{{ old('primary_country_name', @$data['primary_country_name']) }}">
                        <input type="hidden" name="billing_address"
                               value="{{ old('billing_address', @$data['billing_address']) }}">
                        <input type="hidden" name="billing_address_1"
                               value="{{ old('billing_address_1', @$data['billing_address_1']) }}">
                        <input type="hidden" name="billing_city"
                               value="{{ old('billing_city', @$data['billing_city']) }}">
                        <input type="hidden" name="billing_state_name"
                               value="{{ old('billing_state_name', @$data['billing_state_name']) }}">
                        <input type="hidden" name="billing_zip" value="{{ old('billing_zip', @$data['billing_zip']) }}">
                        <input type="hidden" name="billing_country_name"
                               value="{{ old('billing_country_name', @$data['billing_country_name']) }}">
                        <input type="hidden" name="choise_details"
                               value="{{ old('choise_details', @$data['choise_details']) }}">
                        <input type="hidden" name="address_type"
                               value="{{ old('address_type', @$data['address_type']) }}">
                        <input type="hidden" name="amount" value="{{ old('amount', @$data['amount']) }}">
                        <input type="hidden" name="deleveryCast1"
                               value="{{ old('deleveryCast1', @$data['deleveryCast1']) }}">
                        <input type="hidden" name="shippingCharge"
                               value="{{ old('shippingCharge', @$data['shippingCharge']) }}">
                        <input type="hidden" name="discountCharge"
                               value="{{ old('discountCharge', @$data['discountCharge']) }}">
                        <input type="hidden" name="coupon" value="{{ old('coupon', @$data['coupon']) }}">
                        <input type="hidden" name="pk_locations"
                               value="{{ old('pk_locations', @$data['pk_locations']) }}">
                        <input type="hidden" name="estimated_del"
                               value="{{ old('estimated_del', @$data['estimated_del']) }}">
                        <input type="hidden" name="couponCode"
                               value="{{ old('couponCode', @$data['couponCode']) }}">

                        @if(isset($data['item_address']) && count($data['item_address']))
                            @foreach($data['item_address'] as $id => $item_address)
                                <input type="hidden" class="form-control"
                                       name="item_address[{{ $id }}][shipping_full_name]"
                                       value="{{ old('item_address') &&
                                                        !empty(old('item_address.'.$id.'.shipping_full_name')) ?
                                                        old('item_address.'.$id.'.shipping_full_name') : @$item_address['shipping_full_name'] }}">
                                <input type="hidden" class="form-control"
                                       name="item_address[{{ $id }}][shipping_phone]"
                                       value="{{ old('item_address') &&
                                                        !empty(old('item_address.'.$id.'.shipping_phone')) ?
                                                        old('item_address.'.$id.'.shipping_phone') : @$item_address['shipping_phone'] }}">
                                <input type="hidden" class="form-control"
                                       name="item_address[{{ $id }}][shipping_address]"
                                       value="{{ old('item_address') &&
                                                        !empty(old('item_address.'.$id.'.shipping_address')) ?
                                                        old('item_address.'.$id.'.shipping_address') : @$item_address['shipping_address'] }}">
                                <input type="hidden" class="form-control"
                                       name="item_address[{{ $id }}][shipping_address_1]"
                                       value="{{ old('item_address') &&
                                                        !empty(old('item_address.'.$id.'.shipping_address_1')) ?
                                                        old('item_address.'.$id.'.shipping_address_1') : @$item_address['shipping_address_1'] }}">
                                <input type="hidden" class="form-control"
                                       name="item_address[{{ $id }}][shipping_city]"
                                       value="{{ old('item_address') &&
                                                        !empty(old('item_address.'.$id.'.shipping_city')) ?
                                                        old('item_address.'.$id.'.shipping_city') : @$item_address['shipping_city'] }}">
                                <input type="hidden" class="form-control"
                                       name="item_address[{{ $id }}][shipping_state_name]"
                                       value="{{ old('item_address') &&
                                                        !empty(old('item_address.'.$id.'.shipping_state_name')) ?
                                                        old('item_address.'.$id.'.shipping_state_name') : @$item_address['shipping_state_name'] }}">
                                <input type="hidden" class="form-control"
                                       name="item_address[{{ $id }}][shipping_zip]"
                                       value="{{ old('item_address') &&
                                                        !empty(old('item_address.'.$id.'.shipping_zip')) ?
                                                        old('item_address.'.$id.'.shipping_zip') : @$item_address['shipping_zip'] }}">
                                <input type="hidden" class="form-control"
                                       name="item_address[{{ $id }}][shipping_country_name]"
                                       value="{{ old('item_address') &&
                                                        !empty(old('item_address.'.$id.'.shipping_country_name')) ?
                                                        old('item_address.'.$id.'.shipping_country_name', 'USA') : @$item_address['shipping_country_name'] }}">
                                <input type="hidden" class="form-control"
                                       name="item_address[{{ $id }}][same_as_billing]"
                                       value="{{ old('item_address') &&
                                                        !empty(old('item_address.'.$id.'.same_as_billing')) ?
                                                        old('item_address.'.$id.'.same_as_billing') : @$item_address['same_as_billing'] }}">
                                <input type="hidden"
                                       name="item_address[{{ $id }}][delivery_charge]"
                                       value="{{ old('item_address') &&
                                                        !empty(old('item_address.'.$id.'.delivery_charge')) ?
                                                        old('item_address.'.$id.'.delivery_charge') : @$item_address['delivery_charge'] }}">
                            @endforeach
                        @else
                            <input type="hidden" name="item_address">
                        @endif
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection
