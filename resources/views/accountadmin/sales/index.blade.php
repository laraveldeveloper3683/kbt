@extends('layouts.backend_new')

@section('content')
    <!-- Page wrapper  -->
    <!-- ============================================================== -->
    <div class="page-wrapper">
        <!-- ============================================================== -->
        <!-- Container fluid  -->
        <!-- ============================================================== -->
        <div class="container-fluid">
            <!-- ============================================================== -->
            <!-- Bread crumb and right sidebar toggle -->
            <!-- ============================================================== -->
            <div class="row page-titles">
                <div class="col-lg-4 col-md-6 align-self-center">
                    <h4 class="text-themecolor">Sales</h4>
                </div>
                <div class="col-lg-4 col-md-6 align-self-center p-0">
                    <form method="GET" action="{{ route('accountadmin.sales.index') }}">
                        <div class="input-group">
                            <input type="text" class="form-control" placeholder="Search sales..." name="search"
                                   value="{{ old('search', $request->search ?? '') }}" required>
                            <div class="input-group-append">
                                <button class="btn btn-secondary" type="submit">
                                    <i class="fa fa-search"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="col-lg-4 col-md-12 align-self-center text-end pr-lg-3 pr-md-0 mt-lg-0 mt-md-3">
                    <div class="d-flex justify-content-around align-items-center">
                     <a href="{{ route('accountadmin.sales.create') }}">
                            <button type="button" class="btn btn-info d-none d-lg-block m-l-15 text-white"
                                    style="margin-to: -34px;"><i
                                        class="fa fa-plus-circle"></i> Create New
                            </button>
                        </a>
                        <ol class="breadcrumb justify-content-end">
                            <li class="breadcrumb-item"><a href="/accountadmin">Home</a></li>
                            <li class="breadcrumb-item active"><a
                                        href="{{ route('accountadmin.sales.index') }}">Sales</a></li>
                        </ol>
                       
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
                                        <th>Sale Number</th>
                                        <th>Sale Date</th>
                                        <th>Customer Name</th>
                                        <th class="text-right">Subtotal</th>
                                        <th class="text-right">Shipping Charges</th>
                                        <th class="text-right">Tax Rate</th>
                                        <th class="text-right">Tax Amount</th>
                                        <th class="text-right">Discount</th>
                                        <th class="text-right">Amount</th>
                                        <th class="text-center">Is Paid</th>
                                        <th class="text-center">Payment Method</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @forelse($sales as $sale)
                                        <tr onclick="window.location='{{ route('accountadmin.sales.show', $sale->pk_sales) }}'"
                                            style="cursor: pointer">
                                            <td>{{ $sale->pk_sales }}</td>
                                            <td>{{ date('m/d/Y', strtotime($sale->created_at)) }}</td>
                                            <td>
                                                @if($sale->pk_customers)
                                                    <a href="/accountadmin/customers/{{@$sale->pk_customers}}/view">
                                                        {{ $sale->customer_name }}
                                                    </a>
                                                @else
                                                    {{ $sale->customer_name }}
                                                @endif
                                            </td>
                                            <td class="text-right">${{ number_format($sale->subtotal, 2) }}</td>
                                            <td class="text-right">${{  number_format($sale->shippingcharge, 2) }}</td>
                                            <td class="text-right">{{ number_format($sale->tax_total, 2) }}%</td>
                                            <td class="text-right">
                                                @php
                                                    $taxAmount = ($sale->subtotal * $sale->tax_total) / 100;
                                                @endphp
                                                ${{ number_format($taxAmount, 2) }}
                                            </td>
                                            <td class="text-right">${{ number_format($sale->discountCharge, 2) }}</td>
                                            <td class="text-right">
                                                ${{ number_format($sale->subtotal + $taxAmount, 2) }}</td>
                                            <td class="text-center">
                                                @if($sale->is_paid)
                                                    <span class="badge badge-success">Yes</span>
                                                @else
                                                    <span class="badge badge-danger">No</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if($sale->payment_method == 'cash')
                                                    Cash
                                                @elseif($sale->payment_method == 'gift_card')
                                                    Gift Card
                                                @else
                                                    Card
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="100%" class="text-center">
                                                No sales found!
                                            </td>
                                        </tr>
                                    @endforelse
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
