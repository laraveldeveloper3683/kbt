@extends('layouts.backend_new')
@section('content')
    <div class="page-wrapper">
        <div class="container-fluid">
            <div class="row page-titles">
                <div class="col-md-5 align-self-center">
                    <h4 class="text-themecolor">Dashboard</h4>
                </div>
                <div class="col-md-7 align-self-center text-end">
                    <div class="d-flex justify-content-end align-items-center">
                        <ol class="breadcrumb justify-content-end">
                            <li class="breadcrumb-item"><a href="/accountadmin">Home</a></li>
                            <li class="breadcrumb-item active"><a href="/accountadmin/customers">Customer</a></li>
                        </ol>
                    </div>
                </div>
            </div>

            <div class="row justify-content-center mb-5">
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title text-center">
                                Customer Details
                            </h4>
                        </div>
                        <div class="card-body">
                            <table class="table table-striped table-bordered table-hover">
                                <tr>
                                    <th>Name</th>
                                    <td>{{ $customer->customer_name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Email</th>
                                    <td>{{ $customer->email ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Phone</th>
                                    <td>{{ $customer->office_phone ?? 'N/A' }}</td>
                                </tr>
                                @if($customer->website)
                                    <tr>
                                        <th>Website</th>
                                        <td>{{ $customer->website }}</td>
                                    </tr>
                                @endif
                                @if($customer->pk_customer_type)
                                    <tr>
                                        <th>Customer Type</th>
                                        <td>{{ $customer->customertype->customer_type ?? 'N/A' }}</td>
                                    </tr>
                                @endif
                                @if($customer->business_name)
                                    <tr>
                                        <th>Business Name</th>
                                        <td>{{ $customer->business_name }}</td>
                                    </tr>
                                @endif
                                <tr>
                                    <th>Status</th>
                                    <td>
                                        @if($customer->active)
                                            <span class="badge badge-success">Active</span>
                                        @else
                                            <span class="badge badge-success">Inactive</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Login Enabled</th>
                                    <td>
                                        @if($customer->login_enable)
                                            <span class="badge badge-success">Yes</span>
                                        @else
                                            <span class="badge badge-success">No</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            @if($customer->address->count())
                <div class="row justify-content-center mb-5">
                    <div class="{{ $customer->address->count() == 1 ? 'col-md-4' : 'col-md-12' }}">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title text-center">
                                    Customer Addresses
                                </h4>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    @foreach($customer->address as $address)
                                        <div class="{{ $customer->address->count() == 1 ? 'col-md-12' : 'col-md-4' }}">
                                            <table class="table table-striped table-bordered table-hover">
                                                <tr>
                                                    <th>Address</th>
                                                    <td>{{ $address->address ?? 'N/A' }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Address 1</th>
                                                    <td>{{ $address->address_1 ?? 'N/A' }}</td>
                                                </tr>
                                                <tr>
                                                    <th>City</th>
                                                    <td>{{ $address->city ?? 'N/A' }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Zipcode</th>
                                                    <td>{{ $address->zip ?? 'N/A' }}</td>
                                                </tr>
                                                <tr>
                                                    <th>State</th>
                                                    <td>{{ $address->state->state_code ?? 'N/A' }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Country</th>
                                                    <td>USA</td>
                                                </tr>
                                            </table>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
