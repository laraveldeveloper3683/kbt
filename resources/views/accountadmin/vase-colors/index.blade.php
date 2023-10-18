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
                                        <th>Vase Type</th>
                                        <th>Vase Colors</th>
                                        <th style="text-align:center;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                  @foreach($VaseColors as $VaseColor)
                                    <tr>
                                      <td onclick="window.location='{{ route('accountadmin.vase-colors.edit', ['id' => $VaseColor->pk_vase_colors]) }}'">{{$VaseColor->vase_type}}</td>
                                      <td onclick="window.location='{{ route('accountadmin.vase-colors.edit', ['id' => $VaseColor->pk_vase_colors]) }}'">{{$VaseColor->vase_colors}}</td>
                                        <td style="text-align:center;">
                                         <a href="/accountadmin/vase-colors/edit/{{$VaseColor->pk_vase_colors}}"><button class="btn btn-danger text-white">Edit</button></a>
                                         <a href="javascript:" onclick="form_alert('vase-colors-{{$VaseColor->pk_vase_colors}}', '{{'want to delete '}}{{$VaseColor->vase_type}}{{'Vase Color?'}}')"><button class="btn btn-danger text-white">Delete</button></a>
                                         <form action="{{route('accountadmin.vase-colors.delete',[$VaseColor->pk_vase_colors])}}" method="get" id="vase-colors-{{$VaseColor->pk_vase_colors}}">
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
