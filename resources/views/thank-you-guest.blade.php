@extends('layouts.frontend')

@section('title', 'Cart')

@section('content')

    <div class="container">

        <div class="row">

            <div class="jumbotron text-center" style="width:100%">
                @php
                    $thankYou = Helper::getAcknowledge('CUSTOMER_CREATE_ORDER_THANKYOU');
                    $createOrder = Helper::getAcknowledge('CUSTOMER_CREATE_ORDER');
                @endphp
                <h1 class="display-3 text-{{ @$thankYou['messageType'] }}">{{ @$thankYou['message'] }}</h1>
                <p class="lead"><strong>{{ @$createOrder['message'] }}</strong></p>

                <div class="col-md-6 offset-md-3 mb-4 text-left">
                    @if($order->deliveryOption->delivery_or_pickup == 'Store Pickup')
                        <div
                            style="background-color: #FFF;text-align: center;    margin: 0 0 20px 40px;padding-top: 10px;">
                            <!-- <h6><strong>Pickup Location</strong></h6> -->
                            <p class="lead">{{$store->address}} , {{$store->city}}, {{$store->zip}}</p>
                            <ul>
                                @if(isset($store->locationTime->pk_location_times))
                                    <li class="list-group-item d-flex justify-content-between lh-condensed"
                                        style="border:unset;">{{$store->locationTime->day}}
                                        : {{$store->locationTime->open_time}}
                                        To {{$store->locationTime->close_time}}</li>
                                @endif
                            </ul>
                            @if($order->pickup_date)
                                <small class="text-muted">
                                    Selected Pickup Date - {{ @date('m/d/Y', strtotime(@$order->pickup_date)) }}
                                </small>
                            @endif
                        </div>
                    @else
                        @foreach($order_items as $order_item)
                            <div
                                style="background-color: #FFF; text-align: center; margin: 0 0 20px 40px; padding-top: 10px; padding-bottom: 1px;">
                                @php
                                    $itemAddr = $order_item->shippingAddress;
                                @endphp
                                <p class="text-wrap">
                                    For {{ $order_item->name }} :
                                    {{ $itemAddr->shipping_address }} , {{ $itemAddr->shipping_city }}
                                    , {{ $itemAddr->shipping_zip }}
                                </p>
                                @if($itemAddr->delivery_charge)
                                    <p class="text-center font-weight-bold">
                                        Delivery Charge:
                                        ${{ number_format($itemAddr->delivery_charge, 2) }}
                                    </p>
                                @else
                                    <p class="text-center font-weight-bold">
                                        Delivery Charge:
                                        ${{ number_format($order->delivery_charge, 2) }}
                                    </p>
                                @endif
                                @if($itemAddr->delivery_date)
                                    <p class="text-center font-weight-bold">
                                        Estimated Delivery:
                                        {{ date('m/d/Y', strtotime($itemAddr->delivery_date))  }}
                                    </p>
                                @endif
                            </div>
                        @endforeach
                    @endif
                    <?php $total = 0; $total_qty = 0; ?>
                    <ul class="list-group mb-3 sticky-top">
                        @if(!empty($order_items))
                            @foreach($order_items as $item_val)
                                    <?php
                                    $total     += $item_val->price * $item_val->quantity;
                                    $total_qty += $item_val->quantity;
                                    ?>

                                <li class="list-group-item d-flex justify-content-between lh-condensed">
                                    <div>
                                        <h6 class="my-0">{{ $item_val->name }}</h6>
                                        <small class="text-muted">{{ $item_val->description }}</small>
                                    </div>
                                    <span
                                        class="text-muted">${{ number_format($item_val->price * $item_val->quantity, 2) }}</span>
                                </li>

                            @endforeach
                        @endif

                        @if($order->deliveryOption->delivery_or_pickup != 'Store Pickup')
                            @if(isset($order->delivery_charge) && count($order_items) > 0 && count($order_items) == 1)
                                <li class="list-group-item d-flex justify-content-between lh-condensed">
                                    <div>
                                        <h6 class="my-0">Delivery Charge</h6>
                                        <small class="text-muted">
                                            delivering from - {{ @$store->location_name }}
                                            , {{ @$store->city }}
                                        </small><br>
                                        @if($order->delivery_date)
                                            <small class="text-muted">
                                                <strong>Estimated Delivery</strong>
                                                - {{ @date('m/d/Y', strtotime(@$order->delivery_date)) }}
                                            </small>
                                            {{--<br>
                                            <small class="text-muted">
                                                <strong>
                                                    Selected Delivery Date
                                                </strong>
                                                - {{ @date('m/d/Y', strtotime(@$order->delivery_date)) }}
                                            </small>--}}
                                        @endif
                                    </div>
                                    <span class="text-muted">${{ number_format($order->delivery_charge, 2) }}</span>
                                </li>
                            @else
                                <li class="list-group-item d-flex justify-content-between lh-condensed">
                                    <div>
                                        <h6 class="my-0">Delivery Charges</h6>
                                    </div>
                                    <span class="text-muted">${{ number_format($order->delivery_charge, 2) }}</span>
                                </li>
                            @endif
                        @endif
                        @if(isset($order->tax_charge))
                            <li class="list-group-item d-flex justify-content-between lh-condensed">
                                <div>
                                    <h6 class="my-0">Tax</h6>

                                </div>
                                <span class="text-muted">${{ $order->tax_charge }}</span>
                            </li>
                        @endif
                        @if(isset($order->discount_charge))
                            <li class="list-group-item d-flex justify-content-between lh-condensed">
                                <div>
                                    <h6 class="my-0">Discount (-)</h6>

                                </div>
                                @if(isset($order->coupon_discount_type) && ($order->coupon_discount_type=='fixed'))
                                    <span class="text-muted">${{$order->discount_charge}}</span>
                                @endif
                                @if(isset($order->coupon_discount_type) && ($order->coupon_discount_type=='percent'))
                                    <span class="text-muted">{{$order->discount_charge}}%</span>
                                @endif
                            </li>
                        @endif

                        @if($order->deliveryOption->delivery_or_pickup == 'Store Pickup')
                            @php
                                $total = $order->total;
                            @endphp
                        @endif

                        @if($order->deliveryOption->delivery_or_pickup != 'Store Pickup')
                            @php
                                $total += ($order->tax_charge + $order->delivery_charge);
                            @endphp
                        @endif


                        <li class="list-group-item d-flex justify-content-between">
                            <span>Total (USD)</span>
                            @if(!empty($order->discount_charge) && ($order->coupon_discount_type == 'percent') && ($order->deliveryOption->delivery_or_pickup == 'Store Pickup'))
                                <strong>${{ number_format((float)$total-($total*$order->discount_charge/100), 2, '.', '') }}</strong>
                            @endif
                            @if(!empty($order->discount_charge) && ($order->coupon_discount_type == 'fixed') && ($order->deliveryOption->delivery_or_pickup == 'Store Pickup'))
                                <strong>${{number_format((float)$total-($order->discount_charge), 2, '.', '')}}</strong>
                            @endif
                            @if(!empty($order->discount_charge) && ($order->coupon_discount_type == 'percent') && ($order->deliveryOption->delivery_or_pickup != 'Store Pickup'))
                                <strong>${{ number_format((float)$total-($total*$order->discount_charge/100), 2, '.', '')}}</strong>
                            @endif
                            @if(!empty($order->discount_charge) && ($order->coupon_discount_type == 'fixed') && ($order->deliveryOption->delivery_or_pickup != 'Store Pickup'))
                                <strong>${{number_format((float)$total-($order->discount_charge), 2, '.', '')}}</strong>
                            @endif
                            @if(empty($order->discount_charge))
                                <strong>${{number_format($total, 2)}}</strong>
                            @endif
                        </li>
                    </ul>
                    <!--  -->
                </div>

                <hr>
                <p class="lead">
                    <a class="btn btn-success btn-sm" href="{!! route('dashboard.myorderdetails',$pk_orders) !!}"
                       role="button">Track Order</a>
                </p>

                @if(!$order->pk_users)
                    <form action="{{ route('other-checkout-account-create') }}" class="text-left" method="POST">
                        @csrf

                        <input type="hidden" name="pk_orders" value="{{ $pk_orders }}">

                        <div class="form-group text-center">
                            <label for="create-account" class="form-check-label">
                                Want to create an account?
                                <input type="checkbox" value="1" id="create-account" class="form-check-inline ml-2"
                                       name="create_account" {{ old('create_account') == 1 ? 'checked' : '' }}>
                            </label>
                        </div>

                        <div id="create-account-section"
                             style="{{ old('create_account') == 1 ? 'display: block;' : 'display: none;' }}">
                            <div class="form-group">
                                <label for="username">
                                    Username
                                    <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="username" id="username" value="{{ old('username') }}"
                                       class="form-control @error('username') is-invalid @enderror"
                                       placeholder="Enter username">
                                @error('username')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="password">
                                    Password
                                    <span class="text-danger">*</span>
                                </label>
                                <input type="password" name="password" id="password"
                                       class="form-control @error('password') is-invalid @enderror"
                                       placeholder="Enter password">
                                @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="confirm-password">
                                    Confirm Password
                                    <span class="text-danger">*</span>
                                </label>
                                <input type="password" name="password_confirmation" id="confirm-password"
                                       class="form-control @error('password') is-invalid @enderror"
                                       placeholder="Enter password">
                                @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <button type="submit" class="btn btn-primary">Create Account</button>
                        </div>
                    </form>
                @endif
            </div>

        </div>
    </div>

    @if(!$order->pk_users)
        <script type="text/javascript">
            $(document).ready(function () {
                $('#create-account').on('change', function () {
                    if ($(this).is(':checked')) {
                        $('#create-account-section').show();
                    } else {
                        $('#create-account-section').hide();
                    }
                });
            });
        </script>
    @endif
@endsection
