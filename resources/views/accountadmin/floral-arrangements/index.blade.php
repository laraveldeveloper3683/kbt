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
        @include('common.admin-panel-top-area-new-button')
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
                                        <th>Category</th>
                                        <th>Title</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                  @foreach($floralArrangements as $floralArrangement)
                                    <tr>
                                        <td onclick="window.location='{!! route('accountadmin.floral-arrangements.edit', ['id' => $floralArrangement->pk_floral_arrangements]) !!}'">{{$floralArrangement->product_category}}</td>
                                        <td onclick="window.location='{!! route('accountadmin.floral-arrangements.edit', ['id' => $floralArrangement->pk_floral_arrangements]) !!}'">{{$floralArrangement->title}}</td>
                                        <td>
                                            <a href="/accountadmin/floral-arrangements/edit/{{$floralArrangement->pk_floral_arrangements}}"><button class="btn btn-danger text-white">Edit</button></a>

                                            <!-- <a href="/accountadmin/floral-arrangements/delete/{{$floralArrangement->pk_floral_arrangements}}"><button class="btn btn-danger text-white">Delete</button></a> -->

                                            <a href="javascript:" onclick="form_alert('floral-arrangements-{{$floralArrangement->pk_floral_arrangements}}', '{{'want to delete '}}{{$floralArrangement->product_category}}{{' floral arrangements?'}}')"><button class="btn btn-danger text-white">Delete</button></a>
                                            <form action="{{route('accountadmin.floral-arrangements.delete',[$floralArrangement->pk_floral_arrangements])}}" method="get" id="floral-arrangements-{{$floralArrangement->pk_floral_arrangements}}">
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
