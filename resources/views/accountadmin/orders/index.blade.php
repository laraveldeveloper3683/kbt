@extends('layouts.backend_new')

@section('content')
    <!-- Page wrapper  -->
    <!-- ============================================================== -->
    <div class="page-wrapper">
        <!-- ============================================================== -->
        <!-- Container fluid  -->
        <!-- ============================================================== -->
        <div class="container-fluid">
            @if (Session::has('message'))
                <p class="alert alert-{{ Session::get('messageType') }}">{{ Session::get('message') }}</p>
            @endif
            <!-- ============================================================== -->
            <!-- Bread crumb and right sidebar toggle -->
            <!-- ============================================================== -->
            <div class="row page-titles">
                <div class="col-md-2 align-self-center">
                    <h4 class="text-themecolor">Orders</h4>
                </div>
                @include('accountadmin.orders.includes.filter_section')
                <div class="col-md-3 align-self-center text-end">
                    <div class="d-flex justify-content-end align-items-center">
                        <ol class="breadcrumb justify-content-end">
                            <li class="breadcrumb-item"><a href="/accountadmin">Home</a></li>
                            <li class="breadcrumb-item active"><a href="/accountadmin/orders">Orders</a></li>
                        </ol>

                         <a href="{{ route('accountadmin.orders.create') }}"> <button type="button" class="btn btn-info d-none d-lg-block m-l-15 text-white"><i
                                        class="fa fa-plus-circle"></i> Create New</button></a>
                    </div>
                </div>
            </div>
            <!-- ============================================================== -->
            <!-- End Bread crumb and right sidebar toggle -->
            <!-- ============================================================== -->
            <!-- ============================================================== -->
            <!-- Start Page Content -->
            <!-- ============================================================== -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            @if(session('success'))
                                <div class="alert alert-success" role="alert">
                                    {{ session('success') }}
                                </div>
                            @endif

                            @if(session('error'))
                                <div class="alert alert-danger" role="alert">
                                    {{ session('error') }}
                                </div>
                            @endif
                            <!-- <h4 class="card-title">Locations Export</h4>
                                    <h6 class="card-subtitle">Export locations to Copy, CSV, Excel, PDF & Print</h6> -->
                            <div class="table-responsive m-t-40">
                                <table id="example23" class="table table-striped" cellspacing="0" width="100%">
                                    <thead>
                                    <tr>
                                        <th>Order Number</th>
                                        <th style="width: 120px;">Date</th>
                                        <th>Order Status</th>
                                        <th>Customer Name</th>
                                        <th>Delivery Option</th>
                                        <th>Subtotal</th>
                                        <th>Shipping Charge</th>
                                        <th>Tax Rate</th>
                                        <th>Tax Amount</th>
                                        <th>Discount</th>
                                        <th>Amount</th>
                                        {{--  <th>Estimated Delivery</th> --}}
                                        <th>Action</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @if (isset($orderStatusData) && count($orderStatusData))
                                        @foreach ($orderStatusData as $order)
                                            <tr onclick="window.location='/accountadmin/orders/{{ $order->pk_orders }}'"
                                                style="cursor: pointer">
                                                <td>{{ $order->pk_orders }}</td>
                                                <td>{{ date('m/d/Y', strtotime($order->created_at)) }}</td>
                                                <td>{{ strtoupper($order->orderStatus->order_status) ?? 'NEW' }}</td>
                                                <td>
                                                    <a href="/accountadmin/customers/{{@$order->pk_customers}}/view">{{ @$order->customer->customer_name }}</a>
                                                </td>
                                                <td>
                                                    @if($order->deliveryOption)
                                                        @if($order->deliveryOption->delivery_or_pickup == 'Delivery')
                                                            <span
                                                                    class="badge badge-success">{{ $order->deliveryOption->delivery_or_pickup }}</span>
                                                        @else
                                                            <span
                                                                    class="badge badge-warning">{{ $order->deliveryOption->delivery_or_pickup }}</span>
                                                        @endif
                                                    @else
                                                        N/A
                                                    @endif
                                                </td>
                                                <td>${{ number_format($order->subtotal, 2) }}</td>
                                                <td>${{ number_format($order->delivery_charge, 2) }}</td>
                                                <td>{{ number_format($order->tax_charge, 2) }}%</td>
                                                <td>
                                                    @php
                                                        $taxAmount = ($order->subtotal * $order->tax_charge) / 100;
                                                    @endphp
                                                    ${{ number_format($taxAmount, 2) }}
                                                </td>
                                                <td>${{ number_format($order->discount_charge, 2) }}</td>
                                                <td>${{ number_format($order->total, 2) }}</td>
                                                {{--                                                <td>{{ \Carbon\Carbon::parse($order->estimated_del)->isValid() && !is_null($order->estimated_del) ? date('m/d/Y', strtotime($order->estimated_del)) : 'N/A' }}</td>--}}
                                                <td style="width:450px;height:40px;">
                                                    @if (!in_array($order->pk_order_status, [3, 5, 6]))
                                                        <a style="height: 60px;
    width: 167px;"
                                                           href="/accountadmin/orders/cancel/{{ $order->pk_orders }}"
                                                           class="btn btn-primary">Cancel the Order </a>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    @elseif(isset($orders) && count($orders))
                                        @foreach ($orders as $order)
                                            <tr onclick="window.location='/accountadmin/orders/{{ $order->pk_orders }}'"
                                                style="cursor: pointer">
                                                <td>{{ $order->pk_orders }}</td>
                                                <td>{{ date('m/d/Y', strtotime($order->created_at)) }}</td>
                                                <td>{{ strtoupper($order->orderStatus->order_status) ?? 'NEW' }}</td>
                                                <td>
                                                    <a href="/accountadmin/customers/{{@$order->pk_customers}}/view">{{ @$order->customer->customer_name }}</a>
                                                </td>
                                                <td>
                                                    @if($order->deliveryOption)
                                                        @if($order->deliveryOption->delivery_or_pickup == 'Delivery')
                                                            <span
                                                                    class="badge badge-success">{{ $order->deliveryOption->delivery_or_pickup }}</span>
                                                        @else
                                                            <span
                                                                    class="badge badge-warning">{{ $order->deliveryOption->delivery_or_pickup }}</span>
                                                        @endif
                                                    @else
                                                        N/A
                                                    @endif
                                                </td>
                                                <td>${{ number_format($order->subtotal, 2) }}</td>
                                                <td>${{ number_format($order->delivery_charge, 2) }}</td>
                                                <td>{{ number_format($order->tax_charge, 2) }}%</td>
                                                <td>
                                                    @php
                                                        $taxAmount = ($order->subtotal * $order->tax_charge) / 100;
                                                    @endphp
                                                    ${{ number_format($taxAmount, 2) }}
                                                </td>
                                                <td>${{ number_format($order->discount_charge, 2) }}</td>
                                                <td>${{ number_format($order->total, 2) }}</td>
                                                {{--                                                <td>{{ \Carbon\Carbon::parse($order->estimated_del)->isValid() && !is_null($order->estimated_del) ? date('m/d/Y', strtotime($order->estimated_del)) : 'N/A' }}</td>--}}
                                                <td>
                                                    @if (!in_array($order->pk_order_status, [3, 5, 6]))
                                                        <a style="height: 60px;
    width: 167px;"
                                                           href="/accountadmin/orders/cancel/{{ $order->pk_orders }}"
                                                           class="btn btn-primary">Cancel the Order </a>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td class="text-center" colspan="100%">
                                                No orders found!
                                            </td>
                                        </tr>
                                    @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- ============================================================== -->
            <!-- End PAge Content -->
            <!-- ============================================================== -->
        </div>
        <!-- ============================================================== -->
        <!-- End Container fluid  -->
        <!-- ============================================================== -->
    </div>
    <!-- ============================================================== -->
    <!-- End Page wrapper  -->
    <!-- ============================================================== -->
@endsection
