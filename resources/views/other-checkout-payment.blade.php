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
                            {{-- Billing Address Section --}}
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
                                                <input type="text" id="billing_zip" name="billing_zip"
                                                       class="form-control"
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

                            <hr>
                            {{-- Payment Information --}}
                            <div class="row justify-content-center">
                                <div class="col-md-12">
                                    <h4 class="mb-3">Payment Information</h4>

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

                        <input type="hidden" name="choise_details"
                               value="{{ old('choise_details', @$data['choise_details']) }}">
                        <input type="hidden" name="amount" value="{{ old('amount', @$data['amount']) }}">
                        <input type="hidden" name="deleveryCast1"
                               value="{{ old('deleveryCast1', @$data['deleveryCast1']) }}">
                        <input type="hidden" name="shippingCharge"
                               value="{{ old('shippingCharge', @$data['shippingCharge']) }}">
                        <input type="hidden" name="discountCharge"
                               value="{{ old('discountCharge', @$data['discountCharge']) }}">
                        <input type="hidden" name="coupon" value="{{ old('coupon', @$data['coupon']) }}">
                        <input type="hidden" name="store_id" value="{{ old('store_id', @$data['store_id']) }}">
                        <input type="hidden" name="pk_locations"
                               value="{{ old('pk_locations', @$data['pk_locations']) }}">
                        <input type="hidden" name="estimated_del"
                               value="{{ old('estimated_del', @$data['estimated_del']) }}">
                        <input type="hidden" name="couponCode"
                               value="{{ old('couponCode', @$data['couponCode']) }}">
                        <input type="hidden" name="pickup_date"
                               value="{{ old('pickup_date', @$data['pickup_date']) }}">
                        <input type="hidden" name="delivery_date"
                               value="{{ old('delivery_date', @$data['delivery_date']) }}">

                        {{-- Items Shipping Address --}}
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
                                <input type="hidden"
                                       name="item_address[{{ $id }}][delivery_date]"
                                       value="{{ old('item_address') &&
                                                        !empty(old('item_address.'.$id.'.delivery_date')) ?
                                                        old('item_address.'.$id.'.delivery_date') : @$item_address['delivery_date'] }}">
                            @endforeach
                        @else
                            <input type="hidden" name="item_address">
                        @endif
                    </form>
                </div>
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

        function shippingCity(city, address) {
            console.log(city, address)

            $.ajax({
                url     : "{{ url('other-checkoutss') }}",
                type    : 'post',
                dataType: 'json',
                data    : {
                    '_token': '{{ csrf_token() }}',
                    city    : city,
                    address : address
                },
                success : function (data) {
                    console.log("shippingCity -> ", data);
                    $('input[name="deleveryCast1"]').val(data.cost);
                    $('input[name="pk_locations"]').val(data.pk_location);
                    $('input[name="estimated_del"]').val(data.Estimated_Delivery_Time);
                }
            })
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


        var billingAutocomplete;
        var componentForm = {
            street_number: 'short_name',
            //route: 'long_name',
            locality                   : 'long_name',
            administrative_area_level_1: 'short_name',
            country                    : 'long_name',
            postal_code                : 'short_name'
        };

        function addressUpdate(value, fname, city) {
            console.log(value, fname, city);
            $("#" + fname).val(value);
            $('.couponApply').val('');
            $('#couponCode').val('');
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
                    console.log("addressUpdate -> ", data);
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

        function fillInAddress(type) {
            if (type == 'billing') {
                var place = billingAutocomplete.getPlace();
            }

            var new_address = '';
            for (var i = 0; i < place.address_components.length; i++) {
                var addressType = place.address_components[i].types[0];

                if (addressType == 'street_number') {
                    new_address += place.address_components[i]['short_name'];

                    if (type == 'billing') {
                        $('#billing_address').val(new_address);
                    }
                }

                if (addressType == 'route') {
                    if (new_address)
                        new_address += " " + place.address_components[i]['long_name'];
                    else
                        new_address += place.address_components[i]['long_name'];

                    if (type == 'billing') {
                        $('#billing_address').val(new_address);
                    }
                } else if (new_address == '' && addressType == 'locality') {
                    new_address += place.address_components[i]['long_name'];

                    if (type == 'billing') {
                        $('#billing_address').val(new_address);
                    }
                }

                if (componentForm[addressType]) {
                    var val = place.address_components[i][componentForm[addressType]];

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
            // Init Billing Address Autocomplete
            if ($('#billing_address').is(':visible')) {
                initBillingAutocomplete();
            }
        });
    </script>
@endsection
