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
            <div class="col-md-5 align-self-center">
                <h4 class="text-themecolor">Arrangement Type</h4>
            </div>
            <div class="col-md-7 align-self-center text-end">
                <div class="d-flex justify-content-end align-items-center">
                    <ol class="breadcrumb justify-content-end">
                      <li class="breadcrumb-item"><a href="/accountadmin">Home</a></li>
                      <li class="breadcrumb-item active"><a href="/accountadmin/arrangement-type">Arrangement Type</a></li>
                    </ol>
                    <a href="/accountadmin/arrangement-type/add"> <button type="button" class="btn btn-info d-none d-lg-block m-l-15 text-white" style="margin-top: -34px;"><i
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
                        <!-- <h4 class="card-title">Product Category Export</h4>
                        <h6 class="card-subtitle">Export locations to Copy, CSV, Excel, PDF & Print</h6> -->
                        <div class="table-responsive m-t-40">
                            <table id="example23"
                                class="display nowrap table table-hover table-striped border"
                                cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <th>Arrangement Type</th>
                                        <th>Minimum</th>
                                        <th>Maximum</th>
                                        <th style="text-align:center;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                  @foreach($ArrangementTypes as $ArrangementType)
                                    <tr>
                                      <td onclick="window.location='{{ route('accountadmin.arrangement-type.edit', ['id' => $ArrangementType->pk_arrangement_type]) }}'">{{$ArrangementType->arrangement_type}}</td>
                                      <td onclick="window.location='{{ route('accountadmin.arrangement-type.edit', ['id' => $ArrangementType->pk_arrangement_type]) }}'">{{$ArrangementType->minimum_amount}}</td>
                                      <td onclick="window.location='{{ route('accountadmin.arrangement-type.edit', ['id' => $ArrangementType->pk_arrangement_type]) }}'">{{$ArrangementType->maximum_amount}}</td>
                                      <td style="text-align:center;">
                                         <a href="/accountadmin/arrangement-type/edit/{{$ArrangementType->pk_arrangement_type}}"><button class="btn btn-danger text-white">Edit</button></a>
                                         <a href="javascript:" onclick="form_alert('arrangement-type-{{$ArrangementType->pk_arrangement_type}}', '{{'want to delete '}}{{$ArrangementType->arrangement_type}} {{'Arrangement Type?'}}')"><button class="btn btn-danger text-white">Delete</button></a>
                                         <form action="{{route('accountadmin.arrangement-type.delete',[$ArrangementType->pk_arrangement_type])}}" method="get" id="arrangement-type-{{$ArrangementType->pk_arrangement_type}}">
                                         @csrf
                                         </form>
                                      </td>
                                    </tr>
                                    @endforeach
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
