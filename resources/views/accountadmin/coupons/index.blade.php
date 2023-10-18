@extends('layouts.backend_new')

@section('content')
<div class="page-wrapper">
    <div class="container-fluid">
        @include('common.admin-panel-top-area-new-button')

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive m-t-40">
                            <table id="example23"
                                class="display nowrap table table-hover table-striped border"
                                cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <th>Title</th>
                                        <th>Code</th>
                                        <th class="text-center">Discount Amount</th>
                                        <th>Start Date</th>
                                        <th>Expire Date</th>
                                        <th class="text-center">Active</th>
                                        <th style="text-align:center;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(!$data->isEmpty())
                                        @foreach($data as $value)
                                            <tr>
                                                <td onclick="window.location='{!! route('accountadmin.coupons.edit', ['id' => $value->pk_coupons]) !!}'">{!! $value->title !!}</td>
                                                <td onclick="window.location='{!! route('accountadmin.coupons.edit', ['id' => $value->pk_coupons]) !!}'">{!! $value->code !!}</td>
                                                <td onclick="window.location='{!! route('accountadmin.coupons.edit', ['id' => $value->pk_coupons]) !!}'" class="text-center">
                                                    <?php
                                                    if($value->type=='fixed') {
                                                        echo number_format($value->discount_amount,2).' $';
                                                    } else {
                                                        echo number_format($value->discount_amount,2).' %';
                                                    } ?>
                                                </td>
                                                <td onclick="window.location='{!! route('accountadmin.coupons.edit', ['id' => $value->pk_coupons]) !!}'">{!! Helper::formatDate($value->start_at) !!}</td>
                                                <td onclick="window.location='{!! route('accountadmin.coupons.edit', ['id' => $value->pk_coupons]) !!}'">{!! Helper::formatDate($value->expire_at)  !!}</td>

                                                <td onclick="window.location='{!! route('accountadmin.coupons.edit', ['id' => $value->pk_coupons]) !!}'" class="text-center">{{ ($value->active=="1")? "Yes" : "No" }}</td>
                                                <td style="text-align:center;">
                                                    <a href="/accountadmin/coupons/edit/{!! $value->pk_coupons !!}"><button class="btn btn-danger text-white">Edit</button></a>
                                                    <a href="javascript:" onclick="form_alert('coupons-{{$value->pk_coupons}}', '{{'want to delete '}}{{$value->title }} {{'Coupon?'}}')"><button class="btn btn-danger text-white">Delete</button></a>
                                                    <form action="{{route('accountadmin.coupons.delete',[$value->pk_coupons])}}" method="get" id="coupons-{{$value->pk_coupons}}">
                                                    @csrf
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
