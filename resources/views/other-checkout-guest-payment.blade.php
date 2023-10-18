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
                    <form action="{{ url('other-checkout-guest') }}" method="POST">
                        @csrf

                        <div class="card-body">
                            {{-- Billing Address Section --}}
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

                                    <input type="hidden" id="billing_lat" name="billing_lat"
                                           value="{{ old('billing_lat', @$oldData['billing_lat']) }}">

                                    <input type="hidden" id="billing_lng" name="billing_lng"
                                           value="{{ old('billing_lng', @$oldData['billing_lng']) }}">

                                </div>
                            </div>

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
                        <input type="hidden" name="store_time_id"
                               value="{{ old('store_time_id', @$data['store_time_id']) }}">
                        <input type="hidden" name="pk_locations"
                               value="{{ old('pk_locations', @$data['pk_locations']) }}">
                        <input type="hidden" name="pk_location_times"
                               value="{{ old('pk_location_times', @$data['pk_location_times']) }}">
                        <input type="hidden" name="estimated_del"
                               value="{{ old('estimated_del', @$data['estimated_del']) }}">
                        <input type="hidden" name="couponCode"
                               value="{{ old('couponCode', @$data['couponCode']) }}">
                        <input type="hidden" name="pickup_date"
                               value="{{ old('pickup_date', @$data['pickup_date']) }}">

                        {{-- Item address --}}
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
                                <input type="hidden" class="form-control"
                                       name="item_address[{{ $id }}][special_instructions]"
                                       value="{{ old('item_address') &&
                                                        !empty(old('item_address.'.$id.'.special_instructions')) ?
                                                        old('item_address.'.$id.'.special_instructions') : @$item_address['special_instructions'] }}">
                                <input type="hidden"
                                       name="item_address[{{ $id }}][delivery_date]"
                                       value="{{ old('item_address') &&
                                                        !empty(old('item_address.'.$id.'.delivery_date')) ?
                                                        old('item_address.'.$id.'.delivery_date') : @$item_address['delivery_date'] }}">

                                <input type="hidden" name="item_address[{{ $id }}][shipping_lat]"
                                       value="{{ old('item_address') &&
                                                        !empty(old('item_address.'.$id.'.shipping_lat')) ?
                                                        old('item_address.'.$id.'.shipping_lat') : @$item_address['shipping_lat'] }}">

                                <input type="hidden" name="item_address[{{ $id }}][shipping_lng]"
                                       value="{{ old('item_address') &&
                                                        !empty(old('item_address.'.$id.'.shipping_lng')) ?
                                                        old('item_address.'.$id.'.shipping_lng') : @$item_address['shipping_lng'] }}">
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
        var billingAutocomplete;
        var componentForm = {
            street_number: 'short_name',
            //route: 'long_name',
            locality                   : 'long_name',
            administrative_area_level_1: 'short_name',
            country                    : 'long_name',
            postal_code                : 'short_name'
        };

        function addressUpdate() {
            let address = $('#billing_address').val();
            let city = $('#billing_city').val();
            if (!address || !city) {
                console.log("addressUpdate -> address or city is empty")
                return false;
            }

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
                    console.log("addressUpdate -> ", data);
                    $('input[name="deleveryCast1"]').val(data.cost);
                    // $('input[name="pk_locations"]').val(data.pk_location);
                    $('input[name="estimated_del"]').val(data.Estimated_Delivery_Time);
                }
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
                if (place.geometry) {
                    $('#billing_lat').val(place.geometry.location.lat());
                    $('#billing_lng').val(place.geometry.location.lng());
                }
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
                            addressUpdate();
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
