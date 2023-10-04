@extends('layouts.frontend')

@section('title', 'Order Preview')

@section('content')
    <style>
        .modal-backdrop.show {
            display: none !important;
        }
    </style>

    <form action="{{ route('other-checkout-payment-post') }}" method="POST">
        @csrf

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
                                            <th class="align-middle">Phone</th>
                                            <td class="align-middle">
                                                <div class="form-group">
                                                    <input type="text" value="{{ old('phone', @$data['phone']) }}"
                                                           name="phone" required class="form-control"
                                                           placeholder="Enter phone number">
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="align-middle">Email</th>
                                            <td class="align-middle">
                                                <div class="form-group">
                                                    <input type="text" value="{{ old('email', @$data['email']) }}"
                                                           name="email" required class="form-control"
                                                           placeholder="Enter email">
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Delivery Option</th>
                                            <td>
                                                {{ @$deliveryOption->delivery_or_pickup }}
                                            </td>
                                        </tr>
                                        @if(isset($data['couponCode']))
                                            <tr>
                                                <th>Coupon Code</th>
                                                <td>
                                                    {{ @strtoupper(@$data['couponCode']) }}
                                                </td>
                                            </tr>
                                        @endif

                                        @if($deliveryOption->delivery_or_pickup == 'Delivery' && $cartItems && count($cartItems) > 0 && count($cartItems) == 1)
                                            <tr>
                                                <th>Estimated Delivery</th>
                                                <td>
                                                    {{ @$data['delivery_date'] }}
                                                </td>
                                            </tr>
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
                                        @if($deliveryOption->delivery_or_pickup == 'Delivery')
                                            <tr>
                                                <th colspan="2" class="text-right">Delivery Charge</th>
                                                <td>${{ @number_format(@$deliveryCharge , 2)}}</td>
                                            </tr>
                                        @endif
                                        @php
                                            if ($deliveryOption->delivery_or_pickup == 'Delivery') {
                                                $taxTotal = ($total * @$data['shippingCharge']) / 100;
                                                $total += (@$deliveryCharge + $taxTotal) - @$discountedAmount;
                                            } else {
                                                $taxTotal = ($total * @$data['shippingCharge']) / 100;
                                                $total += $taxTotal - @$discountedAmount;
                                            }
                                        @endphp

                                        <tr>
                                            <th colspan="2" class="text-right">Tax</th>
                                            <td>${{ @number_format(@$taxTotal, 2) }}</td>
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
                                    <button type="submit"
                                            class="btn btn-primary float-right">
                                        Confirm Payment
                                        <i class="fa fa-angle-right"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($deliveryOption->delivery_or_pickup == 'Delivery')
                        <div class="col-md-6 mx-auto">
                            <div class="card mt-4 mb-5">
                                <div class="card-header">
                                    <h4 class="card-title text-center">
                                        Shipping Address
                                    </h4>
                                </div>
                                <div class="card-body">
                                    @php
                                        $duplicateItemAddresses = [];
                                    @endphp
                                    @forelse($data['item_address'] as $key => $item)
                                        @php
                                            $address = $item['shipping_address'] . ' ' . $item['shipping_city'] . ' ' . $item['shipping_state_name'] . ' ' . $item['shipping_zip'] . ' ' . $item['delivery_date'];
                                        @endphp

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
                                                <th>Special Instructions</th>
                                                <td>
                                                    {{ @$item['special_instructions'] }}
                                                </td>
                                            </tr>
                                            @if($item['same_as_billing'] == 0 && !in_array($address, $duplicateItemAddresses))
                                                <tr>
                                                    <th>Delivery Charge</th>
                                                    <td>
                                                        ${{ @number_format(@$item['delivery_charge'], 2) }}
                                                    </td>
                                                </tr>
                                            @else
                                                <tr>
                                                    <th>Delivery Charge</th>
                                                    <td>
                                                        Same as other item
                                                    </td>
                                                </tr>
                                            @endif
                                            @if($item['same_as_billing'] == 0 && !in_array($address, $duplicateItemAddresses))
                                                <tr>
                                                    <th>Estimated Delivery</th>
                                                    <td>
                                                        {{ @$item['delivery_date'] }}
                                                    </td>
                                                </tr>
                                            @else
                                                <tr>
                                                    <th>Estimated Delivery</th>
                                                    <td>
                                                        Same as other item
                                                    </td>
                                                </tr>
                                            @endif
                                            @if(isset($cartItems[$key]['card_message']))
                                                <tr>
                                                    <th>Card Message</th>
                                                    <td>
                                                            <textarea name="card_messages[{{ $key }}]" cols="2"
                                                                      data-key="{{ $key }}"
                                                                      class="form-control card-message">{{ @$cartItems[$key]['card_message'] }}</textarea>
                                                    </td>
                                                </tr>
                                            @endif
                                        </table>
                                        @php
                                            $duplicateItemAddresses[$key] = $address;
                                        @endphp
                                    @empty
                                        <table class="table table-hover table-bordered">
                                            <tr>
                                                <td>
                                                    No shipping address found!
                                                </td>
                                            </tr>
                                        </table>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="col-md-6 mx-auto">
                            <div class="card mt-4 mb-5">
                                <div class="card-header">
                                    <h4 class="card-title text-center">
                                        Pickup Address
                                    </h4>
                                </div>
                                <div class="card-body">
                                    <div
                                        style="background-color: #FFF;text-align: center; margin: 0 0 20px 40px; padding-top: 10px;">
                                        <h5>
                                            {{ $location->location_name }}
                                        </h5>
                                        <p>
                                            <strong>Address:</strong>
                                            {{ $location->address . ' ,' . $location->address_1 . ' ,' .
                                                $location->city . ' ,' . $location->zip . ' ,' .
                                                @$location->state->state_code . ' ,' . 'USA' }}
                                        </p>

                                        @if($locationTime)
                                            <p>
                                                <strong>Time:</strong>
                                                {{ 'Day - ' . @$locationTime->day . ' , ' .
                                                    @date('h:i A', @strtotime(@$locationTime->open_time)) . ' -
                                                    ' . @date('h:i A', strtotime($locationTime->close_time)) }}
                                            </p>
                                        @endif

                                        @if(isset($data['pickup_date']))
                                            <p class="text-muted font-weight-bold">
                                                Selected Pickup Date - {{ @$data['pickup_date'] }}
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </form>


    <script type="text/javascript">
        function updateCardMessage(cardMessageInput) {
            $.ajax({
                url   : '{{ url('other-update-card-message') }}',
                method: "put",
                data  : {
                    _token: '{{ csrf_token() }}',
                    id          : cardMessageInput.data('key'),
                    card_message: cardMessageInput.val()
                },
                dataType: "json",
                success : function (response) {
                    console.log('response', response);
                    cardMessageInput.val(response.card_message);
                }
            });
        }

        $(document).ready(function () {
            $('.card-message').on('change', function () {
                let cardMessageInput = $(this);
                updateCardMessage(cardMessageInput);
            });
        });
    </script>
@endsection
